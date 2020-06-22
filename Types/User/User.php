<?php

namespace Fwc\Api\Type;

use Fwc\Api\Auth\Session;

class User extends TypeAbstract implements TypeInterface
{
    protected $table = "user";
    protected $type = "User";

    public function get(): array 
    {
        return parent::get();
    }   
    
    public function erase(string $id): array {
        ;
    }
    
    public function put(string $id): array 
    {
        return parent::put($id);
    }
        
    /**
     * Create new user
     * @param array $queryParams
     * @return array
     */
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
    
    public function delete(string $id): array 
    {
        return parent::delete($id);
    }
    
    public function createSqlTable($type = null): bool {
        return parent::createSqlTable('user');
    }
    
    public function login($params) 
    {
        $email = $params['email'];
        $password = $params['password'];
        
        $where = "`email`='{$email}'";
        $data = parent::read("*", $where);
                
        if (empty($data)) {
            return [ "message" => "User not found" ];
            
        } elseif (password_verify($password, $data[0]['password'])) {
            
            Session::login($data[0]);
            
            return [ "message" => "Session login started" ];
        }
    }
}
