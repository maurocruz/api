<?php
namespace Plinct\Api\Auth;

use Firebase\JWT\JWT;
use Plinct\Api\PlinctApi;
use Plinct\Api\Type\PropertyValue;
use Plinct\Api\Type\User;

class AuthController
{
    public function token($params): ?string {
        $email = $params['email'];
        $password = $params['password'];
        $data = (new User())->get([ "properties" => "*", "email" => $email ]);
        if(isset($data['error'])) {
            return $data['error']['message'];
        } elseif (empty($data)) {
            return null;
        } elseif (password_verify($password, $data[0]['password'])) {
            $value = $data[0];
            $payload = [
                "iss" => PlinctApi::$ISSUER,
                "exp" => time() + PlinctApi::$JWT_EXPIRE,
                "name" => $value['name'],
                "admin" => $value['status'] == 1,
                "uid" => PropertyValue::extractValue($value['identifier'], "id")
            ];
            return JWT::encode($payload, PlinctApi::$JWT_SECRET_API_KEY);
        } else {
            return false;
        }
    }

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
