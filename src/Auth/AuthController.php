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
        
        return [ "message" => "Session login unseted" ];
    }
}
