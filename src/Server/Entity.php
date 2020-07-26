<?php

namespace Plinct\Api\Server;

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
        $data = $this->getData($params);
        
        return $this->buildSchema($params, $data);
    }
    
    protected function getData($params)
    {        
        $filterget = new FilterGet($params, $this->table, $this->properties);
        
        $this->properties = $filterget->getProperties();
                
        $data = parent::read($filterget->field(), $filterget->where(), $filterget->groupBy(), $filterget->orderBy(), $filterget->limit(), $filterget->offset());
                 
        return $data;        
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
     * @param string $id
     * @param type $params
     * @return array
     */
    public function put(array $params): array
    {                
        $idname = "id".$this->table;        
        $idvalue = $params['id'];
        unset($params['id']);
        
        return parent::update($params, "`$idname`=$idvalue");
    }
    
    /**
     * DELETE
     * @param string $id
     * @param type $params
     * @return array
     */
    public function delete(array $params): array
    {
        $filter = new FilterGet($params, $this->table, $this->properties); 
        
        $this->properties = $filter->getProperties();
        
        return parent::erase($filter->where(), $filter->limit());
    }

    /**
     * CREATE SQL
     * @param type $type
     * @return type
     */
    public function createSqlTable($type = null) 
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
