<?php

namespace Plinct\Api\Auth;

class SessionUser 
{        
    private static $NameParam = "userLogin";
    
    private static function startSession()
    {
        if(!isset($_SESSION)) {
            session_start();
        }
    }
    
    public static function getId() 
    {
        return $_SESSION[self::$NameParam]['iduser'];
    }
    
    public static function getName() 
    {
        return $_SESSION[self::$NameParam]['name'];
    }
    
    public static function getEmail() 
    {
        return $_SESSION[self::$NameParam]['email'];
    }
        
    public static function getStatus() 
    {
        return isset($_SESSION[self::$NameParam]['status']) ? $_SESSION[self::$NameParam]['status'] : null;
    }
    
    public static function getStatusWithString($number = null) 
    {
        $status = $number ?? $_SESSION[self::$NameParam]['status'];
        
        switch ($status) {
            case 1:
                return "administrator";

            default:
                return "user";
        }
    }

    public static function login($value) 
    {                       
        $_SESSION[self::$NameParam] = [ 
            "id" => $value['iduser'], 
            "name" => $value['name'], 
            "email" => $value['email'], 
            "status" => $value['status'] 
        ];        
    }
    
    public static function logout() 
    {
        unset($_SESSION[self::$NameParam]);
    }
    
    public static function checkUserAdmin()
    {    
        self::startSession();
        
        return self::getStatus() == '1' ? true : false;
    }
}
