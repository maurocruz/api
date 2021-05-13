<?php
namespace Plinct\Api\Auth;

use Firebase\JWT\JWT;
use Plinct\Cms\App;
use Plinct\Cms\Server\Api;
use Plinct\Tool\ArrayTool;

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
        $data = Api::get('user', [ "properties" => "*", "email" => $email ]);
        // ERROR
        if(isset($data['error'])) return [ "data" => $data['error']['message'], "status" => "Error" ];
        // USER NOT EXISTS
        if (empty($data)) return [ "data" => "User not exists", "status" => "Access unauthorized" ];
        // USER EXISTS BUT NOT ADMIN
        if ($data[0]['status'] != 1) return [ "data" => "User exists but not admin", "status" => "Access unauthorized" ];
        // USER EXISTS
        if (password_verify($password, $data[0]['password'])) {
            $value = $data[0];
            $payload = [
                "iss" => App::getApiHost(),
                "exp" => time() + App::getApiUserExpire(),
                "name" => $value['name'],
                "admin" => $value['status'] == 1,
                "uid" => ArrayTool::searchByValue($value['identifier'], "id")['value']
            ];
            return [ "data" => JWT::encode($payload, App::getApiSecretKey()), "status" => "Access authorized" ];
        }
        // USER NOT AUTHORIZED
        return [ "data" => "User exists", "status" => "Access unauthorized" ];
    }

    /**
     * @param array $params
     * @return false|string
     */
    public function register(array $params) {
        $responseData = Api::post('user', $params);
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
