<?php

namespace Plinct\Api\Auth;

use Plinct\Api\Type\User;

class AuthController extends User
{    
    public function login($params)
    {
        return parent::login($params);
    }    
    
    public function logout()
    {
        SessionUser::logout();
        return [ "message" => "Session login unsetted" ];
    }

    public function register(array $params)
    {
        $responseData = parent::post($params);

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
