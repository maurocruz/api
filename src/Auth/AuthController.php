<?php
namespace Plinct\Api\Auth;

use Firebase\JWT\JWT;
use Plinct\Api\PlinctApi;
use Plinct\Api\Type\User;
use Plinct\Tool\ArrayTool;

class AuthController {

    /**
     * @param $params
     * @return array
     */
    public function login($params): array
    {
        // NO DATA RECEIVED
        if (!isset($params['email']) || !isset($params['password'])) {
            return [
                "status" => "fail",
                "message" => "Access rejected - Incomplete data received",
                "data" => $params
            ];
        }

        $email = filter_var($params['email'], FILTER_VALIDATE_EMAIL);
        $password = $params['password'] ?? null;
        $iss = $params['iss'] ?? PlinctApi::$ISSUER;
        $exp = $params['exp'] ?? PlinctApi::$JWT_EXPIRE;

        // INVALID EMAIL
        if ($email === false) {
            return [
                "status" => "fail",
                "message" => "Invalid email - Access unauthorized",
                "data" => $params
            ];
        }

        // GET DATA
        $data = (new User())->get([ "properties" => "*", "email" => $email ]);

        // ERROR
        if(isset($data['error'])) {
            return [
                "status" => "error",
                "message" => $data['error']['message'],
                "data" => $data
            ];
        }

        // USER NOT EXISTS
        if (empty($data)) {
            return [
                "status" => "fail",
                "message" => "User not exists - Access unauthorized",
                "data" => $data
            ];
        }

        // USER EXISTS
        if (password_verify($password, $data[0]['password'])) {
            $value = $data[0];
            $payload = [
                "iss" => $iss,
                "exp" => time() + $exp,
                "name" => $value['name'],
                "admin" => $value['status'] == 1,
                "uid" => ArrayTool::searchByValue($value['identifier'], "id")['value']
            ];

            return [
                "status" => "success",
                "message" => "Access authorized",
                "token" => JWT::encode($payload, PlinctApi::$JWT_SECRET_API_KEY),
                "data" => $data,
                "payload" => $payload
            ];
        }

        // USER NOT AUTHORIZED
        return [
            "status" => "fail",
            "message" => "User exists - Access unauthorized",
            "data" => $data
        ];
    }

    /**
     * @param array $params
     * @return false|string
     */
    public function register(array $params)
    {
        $responseData = (new User())->post($params);
        // Init application
        if (isset($responseData['error']['code']) && $responseData['error']['code'] == "42S02") {
            return false;
        } elseif (isset($responseData['error']) && isset($responseData['error']['message'])) {
            return $responseData['error']['message'];
        } else {
            return "userAdded";
        }
    }
}
