<?php

namespace Plinct\Api\Server;

use ReflectionClass;
use ReflectionException;

abstract class Entity extends Relationship
{    
    protected $table;
    
    protected $properties = [];
    
    protected $hasTypes = [];
    
    use SchemaTrait;
    
    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array 
    {
        $this->tableHasPart = $this->table;
        $this->params = $params;
        
        if (isset($params['tableHasPart']) && isset($params['idHasPart'])) {
            $data = parent::getRelationship($params['tableHasPart'], $params['idHasPart'], $this->table, $params);
            
        } else {
            $data = $this->getData($params);
        }
        
        return $this->buildSchema($params, $data);
    }
    
    protected function getData($params)
    {        
        $filterGet = new FilterGet($params, $this->table, $this->properties);
        
        $this->properties = $filterGet->getProperties();
                
        return parent::read($filterGet->field(), $filterGet->where(), $filterGet->groupBy(), $filterGet->orderBy(), $filterGet->limit(), $filterGet->offset());
    }
    
    protected function buildSchema($params, $data) 
    {
         if (array_key_exists('error', $data)) {            
            return $data;
            
        } else {    
            // format ItemList            
            if (isset($params['format']) && $params['format'] == "ItemList") {
                if (isset($params['count']) && $params['count'] == "all") {
                    $countAll = parent::read("COUNT(*) as q");
                    $numberOfItems = $countAll[0]['q'];
                    
                } else {
                    $numberOfItems =  count($data);
                }
                
                return $this->listSchema($data, $numberOfItems);
            }
            
            return $this->getSchema($data);
        }
    }
    
    public function post(array $params): array
    {        
        // if relationship
        if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
            return parent::postRelationship($params);
        }        
        
        $message = parent::created($params);
        
        $lastId = parent::lastInsertId();
        
        if ($lastId == '0') {
            return $message;
        } else {
            return [ "id" => $lastId ];
        }
    }
    
    /**
     * PUT
     * @param array $params
     * @return array
     */
    public function put(array $params): array
    {  
        // if relationship
        if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
            return parent::putRelationship($params);
        } 
        unset($params['tableHasPart']);

        $idName = "id".$this->table;
        $idValue = $params['id'];
        unset($params['id']);
        
        return parent::update($params, "`$idName`=$idValue");
    }
    
    /**
     * DELETE
     * @param array $params
     * @return array
     */
    public function delete(array $params): array
    {
        if (isset($params['tableHasPart']) && isset($params['idHasPart'])) {
            return parent::deleteRelationship($params);            
        }
                
        $params = array_filter($params);
        
        $filter = new FilterGet($params, $this->table, $this->properties); 
        
        $this->properties = $filter->getProperties();
        
        return parent::erase($filter->where(), $filter->limit());
    }

    /**
     * CREATE SQL
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null)
    {
        $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
        $reflection = new ReflectionClass($className);
        $sqlFile = dirname($reflection->getFileName()) . "/" . $type . ".sql";

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
