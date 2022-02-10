<?php

declare(strict_types=1);

namespace Plinct\Api\Auth;

use DateInterval;
use DateTime;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Plinct\Api\Type\User;
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
        // VERIFICAR SE É EMAIL VÁLIDO
        $email = filter_var($parseBody['email'], FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
        if (!$email) return [ "status" => "fail", "message" => "Invalid email" ];

        // VERIFICAR SE EXISTE EMAIL
        $resultBd = PDOConnect::run("SELECT * FROM `user` WHERE `email`='$email'");
        if (empty($resultBd)) return ['status' => 'fail', "message" => "Email not registered" ];

        // SE EXISTE GERAR UM TOKEN E SALVAR NO BD
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
        $validator = bin2hex($token);

        $expires = new DateTime('NOW');
        $expires->add(new DateInterval('PT01H')); // 1 hour

        $iduser = $resultBd[0]['iduser'];

        PDOConnect::run("INSERT INTO `passwordReset` (iduser, selector, token, expires) VALUES (:iduser, :selector, :token, :expires);", [
            'iduser' => $iduser,
            'selector' => $selector,
            'token' => hash('sha256', $token),
            'expires' => $expires->format('Y-m-d\TH:i:s')
        ]);

        $mail = self::sendEmailForResetPassword($parseBody, $selector, $validator);

        // RETORNA SUCESSO
        return [ "status" => "success", "message" => "Saved token", "data" => [ "selector" => $selector, "validator" => $validator, "mail" => $mail ] ];
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
        if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password))  return [ "status" => "fail", "message" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character" ];
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
                (new User())->put([ "id" => $iduser, "password" => $newPassword ]);

                $id = $data['id'];
                PDOConnect::run("DELETE FROM `passwordReset` WHERE `id`='$id' OR `expires` < CURRENT_TIMESTAMP()");

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
            return ['status'=>'success','message'=>_("Selector found!"),'data'=>$result[0]];
        } else {
            return ['status'=>'fail','message'=>_("Token invalid or date expired")];
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

        } catch (\PHPMailer\PHPMailer\Exception $e) {
            return [ 'status' => 'fail', 'message' => $phpMail->ErrorInfo ];
        }

    }
}
