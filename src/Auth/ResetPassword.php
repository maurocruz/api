<?php

declare(strict_types=1);

namespace Plinct\Api\Auth;

use DateInterval;
use DateTime;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Plinct\Api\Response\Message\Message;
use Plinct\Api\User\User;
use Plinct\PDO\PDOConnect;

class ResetPassword
{
    /**
     * @param array $parseBody
     * @return array
     * @throws Exception
     */
    public static function resetPassword(array $parseBody): array
    {
	    $email = Validator::index($parseBody,'email');
	    $mailHost = Validator::index($parseBody,'mailHost');
	    $mailUsername = Validator::index($parseBody,'mailUsername');
	    $mailPassword = Validator::index($parseBody,'mailPassword');
	    $urlToResetPassword = Validator::index($parseBody,'urlToResetPassword');

			// VERIFICA SE EXISTEM TODOS OS DADOS
			if (!$email || !$mailHost || !$mailUsername || !$mailPassword || ! $urlToResetPassword)
				return Message::fail()->inputDataIsMissing();

	    // VERIFICAR SE OS DADOS SÃO VÁLIDOS
			if (!Validator::isEmail($email) || !Validator::isEmail($mailUsername))
				return Message::fail()->invalidEmail();

			if (!Validator::isDomain($mailHost))
				return Message::fail()->invalidDomain();

	    if (!Validator::isUrl($urlToResetPassword))
				return Message::fail()->invalidUrl();

      // VERIFICAR SE EXISTE O EMAIL DO USUÁRIO NO BANCO DE DADOS
      $resultBd = PDOConnect::run("SELECT * FROM `user` WHERE `email`='$email'");
			// SE NÃO EXISTE EMAIL
      if (empty($resultBd))
				return Message::fail()->notFoundInDatabase('email');

      $now = new DateTime('NOW');
      $iduser = $resultBd[0]['iduser'];

			// VERIFICA SE EXISTE UM TOKEN NO BD, SE HOUVER DELETA
	    $dataSelect = PDOConnect::run("SELECT * FROM `passwordReset` WHERE `iduser`='$iduser';");
			if (isset($dataSelect[0])) {
					PDOConnect::run("DELETE FROM `passwordReset` WHERE `iduser`='$iduser';");
			}

			// GERAR UM NOVO TOKEN E SALVAR NO BD
			$selector = bin2hex(random_bytes(8));
			$token = random_bytes(32);
			$validator = bin2hex($token);
			$now->add(new DateInterval('PT01H')); // 1 hour
			PDOConnect::run("INSERT INTO `passwordReset` (iduser, selector, token, expires) VALUES (:iduser, :selector, :token, :expires);", [
				'iduser' => $iduser,
				'selector' => $selector,
				'token' => hash('sha256', $token),
				'expires' => $now->format('Y-m-d\TH:i:s')
			]);

			$mail = self::sendEmailForResetPassword($parseBody, $selector, $validator);

      // RETORNA SUCESSO
      return Message::success()->success("Saved token", [ "selector" => $selector, "validator" => $validator, "mail" => $mail ] );
    }

    /**
     * @param array $params
     * @return string[]
     */
    public static function changePassword(array $params): array
    {
        $selector = $params['selector'] ?? null;
        $validator = $params['validator'] ?? null;
        $password = $params['password'] ?? null;
        $repeatPassword = $params['repeatPassword'] ?? null;

        // PASSWORD DOES NOT EQUAL A THE REPEAT
        if ($password !== $repeatPassword) return [ "status" => "fail", "message" => "Password does not equal repeat" ];

        // MISSING DATA
        if (!$selector || !$validator || !$password || !$repeatPassword) return [ "status" => "fail", "message" => "Missing data"];

        // INVALID DATAS
        if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$#", $password))  return [ "status" => "fail", "message" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character" ];
        //
        if (strlen($selector) !== 16 || strlen($validator) !== 64) return [ "status" => "fail", "message" => "Invalid data" ];

        // CHECK SELECTOR AND EXPIRES
        $dataPasswordReset = self::getDataPasswordReset($selector);

        // SUCCESS
        if ($dataPasswordReset['status']=='success') {
            $data = $dataPasswordReset['data'];
            $calc = hash('sha256', hex2bin($validator));

            if (hash_equals($calc, $data['token'])) {
                $iduser = $data['iduser'];
                $newPassword = password_hash($password, PASSWORD_DEFAULT);
                $putData = (new User())->put([ "iduser" => $iduser, "new_password" => $newPassword ]);
								if ($putData['status'] == 'fail') {
									return [ "status" => "fail", "message" => $data['message'] ];
								}
                PDOConnect::run("DELETE FROM `passwordReset` WHERE `iduser`='$iduser';");

                return [ "status" => "success", "message" => "Changed password" ];

            } else {
                return [ "status" => "fail", "message" => "Token invalid" ];
            }
        } else {
            return $dataPasswordReset;
        }
    }

    /**
     * @param $selector
     * @return array
     */
    public static function getDataPasswordReset($selector): array
    {
        $result = PDOConnect::run("SELECT * FROM `passwordReset` WHERE selector = ? AND expires >= NOW()", [$selector]);
        if (!empty($result)) {
            return ['status'=>'success','message'=>"Selector found!",'data'=>$result[0]];
        } else {
            return ['status'=>'fail','message'=>"Token invalid or date expired"];
        }
    }

    /**
     * @param array $parseBody
     * @param string $selector
     * @param string $validator
     * @return array
     */
    private static function sendEmailForResetPassword(array $parseBody, string $selector, string $validator): array
    {
        $resetPasswordUri = $parseBody['urlToResetPassword']."?selector=$selector&validator=$validator";
        $subject = _("Reset password");
        $body = "<p>"._("A new password was requested for this email. If it wasn't you, please disregard this message.")."</p>"
            . "<p>" . sprintf(_("Otherwise, go to this link <a href='%s'>%s</a> and change your password. This token expires in half an hour."), $resetPasswordUri, $resetPasswordUri)."</p>"
            . "<p>"._("regards!")."</p>";

        $phpMail = new PHPMailer(true);

        try {
            $phpMail->isSMTP();
            $phpMail->Host = $parseBody['mailHost'];
            $phpMail->SMTPAuth   = true;
            $phpMail->Username = $parseBody['mailUsername'];
            $phpMail->Password = $parseBody['mailPassword'];
            $phpMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $phpMail->Port = 465;

            $phpMail->setFrom($parseBody['mailUsername'], $subject);

            $phpMail->addAddress($parseBody['email']);

            $phpMail->isHTML();
            $phpMail->Subject = $subject;
            $phpMail->Body = $body;

            $phpMail->send();

            return ['status'=>'success','message'=> sprintf(_("An email has been sent to %s from %s to confirm your identity and change your password."), $parseBody['email'], $parseBody['mailUsername'])];

        } catch (Exception $e) {
            return [ 'status' => 'fail', 'message' => $phpMail->ErrorInfo ];
        }

    }
}
