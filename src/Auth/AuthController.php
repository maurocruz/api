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
        if (isset($responseData['error']) && $responseData['error']['code'] == "42S02") {
            parent::createSqlTable();
            parent::post($params);
        }

        return "userAdded";
    }
}
