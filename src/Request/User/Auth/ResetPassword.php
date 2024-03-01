<?php
declare(strict_types=1);
namespace Plinct\Api\Request\User\Auth;

use DateInterval;
use DateTime;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Plinct\Api\ApiFactory;

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
				return ApiFactory::response()->message()->fail()->inputDataIsMissing();

	    // VERIFICAR SE OS DADOS SÃO VÁLIDOS
			if (!Validator::isEmail($email) || !Validator::isEmail($mailUsername))
				return ApiFactory::response()->message()->fail()->invalidEmail();

			if (!Validator::isDomain($mailHost))
				return ApiFactory::response()->message()->fail()->invalidDomain();

	    if (!Validator::isUrl($urlToResetPassword))
				return ApiFactory::response()->message()->fail()->invalidUrl();

      // VERIFICAR SE EXISTE O EMAIL DO USUÁRIO NO BANCO DE DADOS
	    $resultBd = ApiFactory::server()->connectBd('user')->run("SELECT * FROM `user` WHERE `email`='$email'");
			// SE NÃO EXISTE EMAIL
      if (empty($resultBd))
				return ApiFactory::response()->message()->fail()->propertyNotFoundInDatabase('email');

      $now = new DateTime('NOW');
      $iduser = $resultBd[0]['iduser'];

			// VERIFICA SE EXISTE UM TOKEN NO BD, SE HOUVER DELETA
	    $dataSelect = ApiFactory::server()->connectBd('user')->run("SELECT * FROM `user_passwordReset` WHERE `iduser`='$iduser';");
			if (isset($dataSelect[0])) {
					ApiFactory::server()->connectBd('user')->run("DELETE FROM `user_passwordReset` WHERE `iduser`='$iduser';");
			}

			// GERAR UM NOVO TOKEN E SALVAR NO BD
			$selector = bin2hex(random_bytes(8));
			$token = random_bytes(32);
			$validator = bin2hex($token);
			$now->add(new DateInterval('PT01H')); // 1 hour
			ApiFactory::server()->connectBd('user')->run("INSERT INTO `user_passwordReset` (iduser, selector, token, expires) VALUES (:iduser, :selector, :token, :expires);", [
				'iduser' => $iduser,
				'selector' => $selector,
				'token' => hash('sha256', $token),
				'expires' => $now->format('Y-m-d\TH:i:s')
			]);

			$mail = self::sendEmailForResetPassword($parseBody, $selector, $validator);

      // RETORNA SUCESSO
      return ApiFactory::response()->message()->success("Saved token", [ "selector" => $selector, "validator" => $validator, "mail" => $mail ] );
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
        if ($password !== $repeatPassword)
					return ApiFactory::response()->message()->fail()->passwordRepeatIsIncorrect();

        // MISSING DATA
        if (!$selector || !$validator || !$password || !$repeatPassword)
					return ApiFactory::response()->message()->fail()->inputDataIsMissing();

        // INVALID DATAS
        if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$#", $password))
					return ApiFactory::response()->message()->fail()->passwordLeastLength();
        //
        if (strlen($selector) !== 16 || strlen($validator) !== 64)
					return ApiFactory::response()->message()->fail()->invalidData();

        // CHECK SELECTOR AND EXPIRES
        $dataPasswordReset = self::getDataPasswordReset($selector);

        // SUCCESS
        if ($dataPasswordReset['status']=='success') {
            $data = $dataPasswordReset['data'];
            $calc = hash('sha256', hex2bin($validator));

            if (hash_equals($calc, $data['token'])) {
                $iduser = $data['iduser'];
                $newPassword = password_hash($password, PASSWORD_DEFAULT);
								$putData = ApiFactory::server()->user()->httpRequest()->setPermission()->put([ "iduser" => $iduser, "password" => $newPassword ]);
								if (isset($putData['status']) && $putData['status'] == 'fail') {
									return ApiFactory::response()->message()->fail()->generic($data);
								}
								ApiFactory::server()->connectBd('passwordReset')->delete(['iduser'=>$iduser]);

                return ApiFactory::response()->message()->success("Changed password");

            } else {
                return ApiFactory::response()->message()->fail()->invalidToken();
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
        $result = ApiFactory::server()->connectBd('user')->run("SELECT * FROM `user_passwordReset` WHERE selector = ? AND expires >= NOW()", [$selector]);
        if (!empty($result)) {
            return ApiFactory::response()->message()->success("Selector found!",$result[0]);
        } else {
            return ApiFactory::response()->message()->fail()->invalidToken();
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

						$message = sprintf(_("An email has been sent to %s from %s to confirm your identity and change your password."), $parseBody['email'], $parseBody['mailUsername']);
            return ApiFactory::response()->message()->success($message);

        } catch (Exception $e) {
            return ApiFactory::response()->message()->error()->anErrorHasOcurred($phpMail->ErrorInfo);
        }

    }
}
