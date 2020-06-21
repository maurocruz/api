<?php

namespace Fwc\Api\Type;

class User extends TypeAbstract implements TypeInterface
{
    protected $table = "user";
    protected $type = "User";


    public function get(array $args): array 
    {
        ;
    }
    
    public function post(array $queryParams): array 
    {        
        if ( strlen($queryParams['name']) < 2 ) {                      
            return [ "error" => [
                "message" => "The name must be longer than 2 characters"
            ]];
        }
        
        if (filter_var($queryParams['email'], FILTER_VALIDATE_EMAIL) === false) {
            return [ "error" => [
                "message" => "Invalid email" 
            ]];
        }        
                
        if(strlen($queryParams['password']) < 8) {  
            return [ "error" => [
                "message" => "Password must be a minimum of 8 characters" 
            ]];
        }
        
        if(preg_match('@[A-Z]@', $queryParams['password']) === 0) {  
            return [ "error" => [
                "message" => "Password must contain at least one uppercase character" 
            ]];
        }
        
        if(preg_match('@[a-z]@', $queryParams['password']) === 0) {  
            return [ "error" => [
                "message" => "Password must contain at least one lowercase character" 
            ]];
        }
        
        if(preg_match('@[0-9]@', $queryParams['password']) === 0) {  
            return [ "error" => [
                "message" => "Password must contain at least 1 number" 
            ]];
        }
                
        $queryParams['password'] = password_hash($queryParams['password'], PASSWORD_DEFAULT);
        
        return parent::created($queryParams);
    }
    
    public function createSqlTable($type = null): bool 
    {
        return parent::createSqlTable('user');
    }
}
