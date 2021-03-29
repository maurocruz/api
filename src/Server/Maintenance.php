<?php
namespace Plinct\Api\Server;

use Plinct\Api\Type\User;
use Plinct\PDO\PDOConnect;

class Maintenance {

    public function createSqlTable($type): array {
        $table = substr_replace($type, strtolower(substr($type, 0, 1)), 0, 1);
        $query = "SHOW TABLES LIKE '$table';";
        $data = PDOConnect::run($query);
        if (empty($data)) {
            $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
            return (new $className())->createSqlTable($type);
        } else {
            return [ "message" => $type. " already exists" ];
        }
    }
    
    public function start($userAdmin, $emailAdmin, $passwordAdmin): array {
        $this->createSqlTable('Thing');
        $this->createSqlTable('User');
        $this->createSqlTable('Person');
        // create admin user
        $data = (new User())->post([ "name" => $userAdmin, "email" => $emailAdmin, "password" => $passwordAdmin, "status" => 1 ]);
        return [ "message" => "Basic types created", "data" => $data ];
    }
}
