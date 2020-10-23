<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Auth\SessionUser;
use ReflectionException;

class User extends Entity implements TypeInterface
{
    protected $table = "user";
    
    protected $type = "User";
    
    protected $properties = [ "name", "status" ];

    /**
     * GET
     * @param array|null $params
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
     * @param array $params
     * @return array
     */
    public function put(array $params): array 
    {
        return parent::put($params);
    }

    /**
     * DELETE
     * @param array $params
     * @return array
     */
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
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

        if(isset($data['error'])) {
            return $data;

        } elseif (empty($data)) {
            return [ "message" => "User not found" ];
            
        } elseif (password_verify($password, $data[0]['password'])) {
            SessionUser::login($data[0]);
            return [ "message" => "Session login started" ];
            
        } else {
            return [ "message" => "Password invalid" ];
        }
    }
}
