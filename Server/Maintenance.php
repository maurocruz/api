<?php

namespace Fwc\Api\Server;

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
            $className = "\\Fwc\\Api\\Type\\".$type;
            return (new $className($this->request))->createSqlTable($type);
            
        } else {
            return false;
        }
    }
    
    public function start() 
    {
        $this->createSqlTable('Thing');
        $this->createSqlTable('User');
        $this->createSqlTable('Person');
        
        return [ "response" => "Basic types created" ];
    }
}
