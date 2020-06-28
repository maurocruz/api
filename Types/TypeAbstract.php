<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Crud;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class TypeAbstract extends Crud
{
    protected $request;
    
    protected $table;
    
    protected $properties = [];
    
    protected $withTypes = [];

    use SchemaTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function get(array $params): array 
    {
        $filterget = new FilterGet($params, $this->table, $this->properties);
        
        $this->properties = $filterget->getProperties();
                
        $data = parent::read($filterget->field(), $filterget->where(), $filterget->groupBy(), $filterget->orderBy(), $filterget->limit(), $filterget->offset());
        
        if (array_key_exists('error', $data)) {            
            return $data;
            
        } else {        
            return $this->listSchema($data);
        }
    }
    
    public function post(array $params): array 
    {
        $action = $this->request->getParsedBody()['action'] ?? null;
        if ($action == 'create') {            
            return $this->createSqlTable();                
        }
        
        return parent::created($params);
    }

    public function put(string $id, $params): array
    {   
        $rel = $this->putOnRelationship($id, $params);
        $params = $rel['params'];
        $response = $rel['response'];
                
        $idname = "id".$this->table;        
        $idvalue = $this->request->getAttribute('id');
        
        $response[] = parent::update($params, "`$idname`=$idvalue");
        
        return $response;
    }
    
    private function putOnRelationship($id, $params) 
    {
        $columns = parent::getQuery("SHOW COLUMNS FROM $this->table");
        
        foreach ($columns as $valueColumns) {
            $propColumns[] = $valueColumns['Field'];
        }
        foreach ($params as $keyParams => $valueParams) {
            if (!in_array($keyParams, $propColumns)) {
                $paramRel[$keyParams] = $valueParams;
                unset($params[$keyParams]);
            }
        }
        // if params element relationship
        foreach ($paramRel as $key => $value) {    
            if(array_key_exists($key, $this->withTypes)) {                
                $relationship = new \Fwc\Api\Server\Relationships();
                $response[] = $relationship->putRelationship($this->table, $id, $key, $value);
            }
        }
        
        return [ "params" => $params, "response" => $response ];
    }

    public function delete(string $id, $params): array
    {
        if ($params) { // delete relationship
            foreach ($params as $key => $value) {
                $response[] = (new \Fwc\Api\Server\Relationships())->deleteRelationship($this->table, $id, $key, $value);
            }
            return $response;
            
        } else {        
            $idname = 'id'.$this->table;
            return parent::erase([ $idname => $id ]);
        }
    }

    protected function createSqlTable($type = null) 
    {
        $dir = realpath(__DIR__ . "/../Types/" . ucfirst($type));               
        $sqlFile = $dir."/".$type.".sql";        
        
        // run sql
        if (file_exists($sqlFile)) {
            $data = parent::getQuery(file_get_contents($sqlFile));
            if (array_key_exists("error", $data)) {
                return $data;
            }
            return [ "message" => "Sql table for ".$type. " created successfully!" ];
           
        } else {
            return [ "message" => "Sql table for ".$type." not created!" ];
        }
    }
}
