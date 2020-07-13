<?php

namespace Plinct\Api\Type;

use Plinct\Api\Auth\Session;

class User extends TypeAbstract implements TypeInterface
{
    protected $table = "user";
    protected $type = "User";

    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params = null): array 
    {
        return parent::get($params);
    } 
        
    /**
     * Create new user
     * @param array $params
     * @return array
     */
    public function post(array $params): array 
    {        
        if (strlen($params['name']) < 2 ) {                      
            return [ "error" => [
                "message" => "The name must be longer than 2 characters"
            ]];
        }
        
        if (filter_var($params['email'], FILTER_VALIDATE_EMAIL) === false) {
            return [ "error" => [
                "message" => "Invalid email" 
            ]];
        }        
                
        if(strlen($params['password']) < 8) {  
            return [ "error" => [
                "message" => "Password must be a minimum of 8 characters" 
            ]];
        }
        
        if(preg_match('@[A-Z]@', $params['password']) === 0) {  
            return [ "error" => [
                "message" => "Password must contain at least one uppercase character" 
            ]];
        }
        
        if(preg_match('@[a-z]@', $params['password']) === 0) {  
            return [ "error" => [
                "message" => "Password must contain at least one lowercase character" 
            ]];
        }
        
        if(preg_match('@[0-9]@', $params['password']) === 0) {  
            return [ "error" => [
                "message" => "Password must contain at least 1 number" 
            ]];
        }
                
        $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
        
        return parent::created($params);
    }
    
    
    /**
     * PUT
     * @param string $id
     * @param type $params
     * @return array
     */
    public function put(string $id, $params = null): array 
    {
        return parent::put($id);
    }
    
    /**
     * DELETE
     * @param string $id
     * @return array
     */
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }
    
    /**
     * 
     * @param type $type
     * @return type
     */
    public function createSqlTable($type = null)
    {
        return parent::createSqlTable('User');
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
