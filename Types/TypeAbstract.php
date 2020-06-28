<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Crud;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class TypeAbstract extends Crud
{
    protected $request;
    protected $table;

    use SchemaTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    protected function get(array $params): array 
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
    
    protected function post(array $params): array 
    {
        $action = $this->request->getParsedBody()['action'] ?? null;
        if ($action == 'create') {            
            return $this->createSqlTable();                
        }
        
        return parent::created($params);
    }

    protected function put(string $id, $params): array
    {
        // if params element relationship
        foreach ($params as $key => $value) {
            
            if(in_array($key, $this->propertiesHasTypes)) {                
                $relationship = new \Fwc\Api\Server\Relationships();
                $query = $relationship->putRelationship($this->table, $id, $key, $value);
                
                unset($params[$key]);
            }
        }
        
        $idname = "id".$this->table;        
        $idvalue = $this->request->getAttribute('id');
        
        return parent::update($params, "`$idname`=$idvalue");        
    }

    protected function delete(string $id): array
    {
        $idname = 'id'.$this->table;
        return parent::erase([ $idname => $id ]);
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
