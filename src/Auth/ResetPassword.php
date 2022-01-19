<?php

declare(strict_types=1);

namespace Plinct\Api\Auth;

use DateInterval;
use DateTime;
use Exception;
use Plinct\Api\Type\User;
use Plinct\PDO\PDOConnect;

class ResetPassword
{
    /**
     * @param string $email
     * @return array
     * @throws Exception
     */
    public static function resetPassword(string $email): array
    {
        // VERIFICAR SE É EMAIL VÁLIDO
        $email = filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
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

        // RETORNA SUCESSO
        return [ "status" => "success", "message" => "Saved token", "data" => [ "selector" => $selector, "validator" => $validator ] ];
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
        if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#", $password))  return [ "status" => "fail", "message" => "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character." ];
        //
        if (strlen($selector) !== 16 || strlen($validator) !== 64) return [ "status" => "fail", "message" => "Invalid data" ];

        // CHECK SELECTOR AND EXPIRES
        $results = PDOConnect::run("SELECT * FROM `passwordReset` WHERE selector = ? AND expires >= NOW()", [$selector]);

        // SUCCESS
        if (!empty($results)) {
            $calc = hash('sha256', hex2bin($validator));
            if (hash_equals($calc, $results[0]['token'])) {
                $iduser = $results[0]['iduser'];
                $newPassword = password_hash($password, PASSWORD_DEFAULT);
                (new User())->put([ "id" => $iduser, "password" => $newPassword ]);

                $id = $results[0]['id'];
                PDOConnect::run("DELETE FROM `passwordReset` WHERE `id`='$id' OR `expires` < CURRENT_TIMESTAMP()");

                return [ "status" => "success", "message" => "Changed password" ];

            } else {
                return [ "status" => "success", "message" => "Token invalid" ];
            }
        }

        // TOKEN INVALID OR DATE EXPIRED
        return [ "status" => "fail", "message" => "Token invalid or date expired" ];
    }
}