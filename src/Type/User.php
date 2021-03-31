<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class User extends Entity implements TypeInterface {
    protected $table = "user";
    protected $type = "User";
    protected $properties = [ "name", "status" ];

    public function post(array $params): array {
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

    public function createSqlTable($type = null) : array {
        return parent::createSqlTable('User');
    }
}
