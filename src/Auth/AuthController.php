<?php
namespace Plinct\Api\Auth;

use Firebase\JWT\JWT;
use Plinct\Api\PlinctApi;
use Plinct\Api\Type\PropertyValue;
use Plinct\Api\Type\User;

class AuthController {

    /**
     * @param $params
     * @return array|string[]
     */
    public function login($params): array {
        $email = filter_var($params['email'], FILTER_VALIDATE_EMAIL);
        $password = $params['password'] ?? null;
        // INVALID EMAIL
        if ($email === false) return [ "data" => "Invalid email", "status" => "Access unauthorized" ];
        // GET DATA
        $data = (new User())->get([ "properties" => "*", "email" => $email ]);
        // ERROR
        if(isset($data['error'])) return [ "data" => $data['error']['message'], "status" => "Error" ];
        // USER NOT EXISTS
        elseif (empty($data)) return [ "data" => "User not exists", "status" => "Access unauthorized" ];
        // USER AUTHORIZED
        elseif (password_verify($password, $data[0]['password'])) {
            $value = $data[0];
            $payload = [
                "iss" => PlinctApi::$ISSUER,
                "exp" => time() + PlinctApi::$JWT_EXPIRE,
                "name" => $value['name'],
                "admin" => $value['status'] == 1,
                "uid" => PropertyValue::extractValue($value['identifier'], "id")
            ];
            return [ "data" => JWT::encode($payload, PlinctApi::$JWT_SECRET_API_KEY), "status" => "Access authorized" ];
        }
        // USER NOT AUTHORIZED
        else return [ "data" => "User exists", "status" => "Access unauthorized" ];
    }

    /**
     * @param array $params
     * @return false|string
     */
    public function register(array $params) {
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
