<?php

namespace Plinct\Api\Server;

use Plinct\Api\src\Types\Person\User;

class Maintenance extends Crud
{
    public function createSqlTable($type) 
    {
        $table = substr_replace($type, strtolower(substr($type, 0, 1)), 0, 1);

        $query = "SHOW TABLES LIKE '$table';";
        $data = parent::getQuery($query);
        
        if (empty($data)) {
            $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
            return (new $className())->createSqlTable($type);
            
        } else {
            return [ "message" => $type. " already exists" ];
        }
    }
    
    public function start($userAdmin, $emailAdmin, $passwordAdmin)
    {
        $this->createSqlTable('Thing');
        $this->createSqlTable('User');
        $this->createSqlTable('Person');
        
        // create admin user
        $data = (new User())->post([ "name" => $userAdmin, "email" => $emailAdmin, "password" => $passwordAdmin, "status" => 1 ]);
        
        return [ "message" => "Basic types created", "data" => $data ];
    }
}
