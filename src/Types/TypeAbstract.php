<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Crud;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class TypeAbstract extends Crud
{
    protected $request;
    
    protected $table;
    
    protected $properties = [];
    
    protected $withTypes = [];

    use SchemaTrait;

    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }
    
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
    
    /**
     * GET WITH RELATIONSHIPS
     * @param type $params
     * @return type
     */
    public function getWithPartOf($params) 
    {
        $tableOwner = $params['tableOwner'];
        $tableHas = $tableOwner."_has_".$this->table;
        
        $idOwnerName = "id$tableOwner";
        $idOwner = $params['idOwner'];
        $orderBy = isset($params['orderBy']) ? $params['orderBy']." ".$params['ordering'] : null;
        
        $query = "SELECT * FROM $this->table, $tableHas";
        $query .= " WHERE $tableHas.id$this->table=$this->table.id$this->table";
        $query .= $idOwner ? " AND $tableHas.$idOwnerName=$idOwner" : null;
        $query .= $orderBy ? " ORDER BY $orderBy" : null;
        $query .= ";";
        
        $data = parent::getQuery($query);
        
        if (array_key_exists('error', $data)) {
            return $data;
        }
        
        if (isset($params['format']) && $params['format'] == "ItemList") {
            return $this->formatItemList($params, $data);
        }
        
        return $this->getSchema($data);
    }
    
    private function formatItemList($params, $data)
    {
        if (isset($params['count']) && $params['count'] == "all") {
            $countAll = parent::read("COUNT(*) as q");
            $numberOfItems = $countAll[0]['q'];

        } else {
            $numberOfItems =  count($data);
        }

        return $this->listSchema($data, $numberOfItems);
    }
    
    /**
     * POST
     * @param array $params
     * @return array
     */
    public function post(array $params): array 
    {
        // CREATE TABLE SQL IF NOT EXISTS
        if ($this->request) {        
            $action = $this->request->getParsedBody()['action'] ?? null;

            if ($action == 'create') {            
                return $this->createSqlTable();                
            }
        }
        
        $message = parent::created($params);
        
        return [ "id" => parent::lastInsertId() ];
    }
    
    protected function postRelationship(array $params) 
    {                
        $tableOwner = $params['tableOwner'];
        unset($params['tableOwner']);
        $idOwner = $params['idOwner'];
        unset($params['idOwner']);
        $tableIsPartOf = $params['tableIsPartOf'];
        unset($params['tableIsPartOf']);
        $idIsPartOf = $params['idIsPartOf'];
        unset($params['idIsPartOf']);
                
        return parent::createdRelationship($tableOwner, $idOwner, $tableIsPartOf, $idIsPartOf, $params);
    }

    /**
     * PUT
     * @param string $id
     * @param type $params
     * @return array
     */
    public function put(string $id, $params): array
    {   
        
        $rel = $this->putInRelationship($id, $params);
        
        $params = $rel['params'];
        $response = $rel['response'];
                
        $idname = "id".$this->table;        
        $idvalue = $id;
        
        $response[] = parent::update($params, "`$idname`=$idvalue");
        
        return $response;
    }
    
    /**
     * PUT RELATIONSHIPS
     * @param type $id
     * @param type $params
     * @return type
     */
    private function putInRelationship($id, $params) 
    {        
        $tableOwner = $params['tableOwner'];
        unset($params['tableOwner']);
        $idOwner = $params['idOwner'];
        unset($params['idOwner']);
        $tableIsPartOf = $this->table;
        $idIsPartOf = $id;
        
        
        // check which properties exists in table
        $columns = parent::getQuery("SHOW COLUMNS FROM $this->table");
        
        // build array with fields columns bd        
        foreach ($columns as $valueColumns) {
            $propColumns[] = $valueColumns['Field'];
        }
        
        // compare params with fields
        foreach ($params as $keyParams => $valueParams) {
            // build array with relational params
            if (!in_array($keyParams, $propColumns)) {
                $paramRel[$keyParams] = $valueParams;
                unset($params[$keyParams]);
            }
        }
        
        if (isset($paramRel)) {
            
            parent::updateRelationship($tableOwner, $idOwner, $tableIsPartOf, $idIsPartOf, $paramRel);
            
            $response[] = [ "message" => "Relational table updated" ];
            
        } else {
            $response[] = [ "message" => "No relational params" ];
        }
        
        return [ "params" => $params, "response" => $response ];
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
