<?php

namespace Plinct\Api\Server;

use Psr\Http\Message\ServerRequestInterface as Request;

class Maintenance extends Crud 
{
    protected  $request;
    
    public function __construct(Request $request) 
    {
        $this->request = $request;
    }
    
    public function createSqlTable($type) 
    {
        $table = substr_replace($type, strtolower(substr($type, 0, 1)), 0, 1);
                
        $query = "SHOW TABLES LIKE '$table';";
        $data = parent::getQuery($query);
        
        if (empty($data)) {
            $className = "\\Plinct\\Api\\Type\\".$type;
            return (new $className($this->request))->createSqlTable($type);
            
        } else {
            return [ "message" => $type. " already exists" ];
        }
    }
    
    public function start() 
    {
        $this->createSqlTable('Thing');
        $this->createSqlTable('User');
        $this->createSqlTable('Person');
        
        // create admin user
        $data = (new \Plinct\Api\Type\User($this->request))->post([ "name" => PDOConnect::getUsernameAdmin(), "email" => PDOConnect::getEmailAdmin(), "password" => PDOConnect::getPasswordAdmin(), "status" => 1 ]);
        
        return [ "message" => "Basic types created", "data" => $data ];
    }
}
