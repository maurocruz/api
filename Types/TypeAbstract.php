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
        $filterget = new FilterGet($params, $this->table, $this->properties);
        
        $this->properties = $filterget->getProperties();
                
        $data = parent::read($filterget->field(), $filterget->where(), $filterget->groupBy(), $filterget->orderBy(), $filterget->limit(), $filterget->offset());
        
        if (array_key_exists('error', $data)) {            
            return $data;
            
        } else {   
            // format ItemList
            if (isset($params['format']) && $params['format'] == "ItemList") {
                $numberOfItems = parent::read("COUNT(*) as q");
                return $this->listSchema($data, $numberOfItems[0]['q']);
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
        
        $query = "SELECT * FROM $this->table, $tableHas";
        $query .= " WHERE $tableHas.id$this->table=$this->table.id$this->table";
        $query .= $idOwner ? " AND $tableHas.$idOwnerName=$idOwner" : null;
        $query .= ";";
        
        $data = parent::getQuery($query);
        
        if (array_key_exists('error', $data)) {
            return $data;
        }
        
        return $this->getSchema($data);
    }
    
    /**
     * POST
     * @param array $params
     * @return array
     */
    public function post(array $params): array 
    {
        $action = $this->request->getParsedBody()['action'] ?? null;
        if ($action == 'create') {            
            return $this->createSqlTable();                
        }
        
        return parent::created($params);
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
        $idvalue = $this->request->getAttribute('id');
        
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
        // check if properties exists with fields
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
        
        // if params element relationship
        foreach ($paramRel as $key => $value) {  
            if(array_key_exists($key, $this->withTypes)) {                
                $relationship = new \Fwc\Api\Server\Relationships();
                $response[] = $relationship->putRelationship($this->table, $id, $key, $value);
            } else {
                $response[] = [ "message" => "No relational params" ];
            }
        }
        
        return [ "params" => $params, "response" => $response ];
    }

    /**
     * DELETE
     * @param string $id
     * @param type $params
     * @return array
     */
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
