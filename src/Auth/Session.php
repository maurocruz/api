<?php

namespace Plinct\Api\Auth;

class Session 
{
    public static function login($value) 
    {
        $_SESSION['userLogin'] = [
            "id" => $value['iduser'],
            "name" => $value['name'],
            "email" => $value['email'],
            "status" => $value['status']
        ];
    }
    
    public static function logout() 
    {
        unset($_SESSION['userLogin']);
    }
    
    public static function checkUserAdmin()
    {
        $user = $_SESSION['userLogin'] ?? null;     
        return $user['status'] == '1' ? true : false;
    }
}
