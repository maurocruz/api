<?php

namespace Plinct\Api\Server;

class FilterGet 
{   
    // properties not exists
    private $noWhere = [ "orderBy", "ordering", "limit", "groupBy", "offset", "id", "properties", "where", "format", "count", "fields", "allDetails", "tableHasPart", "idHasPart" ];
        
    // conditions sql
    private $fields = "*";
    private $where;
    private $groupBy;
    private $orderBy;
    private $limit = 200;
    private $offset;
    private $ordering = "Unordering";
    
    private $table;
    
    private $properties;
            
    public function __construct($queryParams, $table, $properties) 
    {        
        $this->table = $table;
                
        $this->properties = isset($queryParams['allDetails']) ? ["*"] : $properties;
        
        if (!empty($queryParams)) {
            $this->setQueries($queryParams);
        }
    }
    
    private function setQueries($queryParams)
    {   
        // fieds
        $this->fields = $queryParams['fields'] ?? $this->fields;
                
        // query params        
        foreach ($queryParams as $key => $value) {
            
            $idname = "id".$this->table;
            if ($value == "id") {
                $value = $idname;
            }            
            
            // ORDER BY
            if (stripos($key, "orderBy") !== false) {
                $this->ordering = $queryParams['ordering'] ?? 'ASC';
                $this->orderBy = stripos($this->ordering, 'rand') !== false ? "rand()" : $value." ".$this->ordering;
            }
            
            // WHERE
              // like
            $like = stristr($key,"like", true);
            if ($like) {
                $whereArray[] = "`$like` LIKE '%$value%'";

            } elseif (!in_array($key, $this->noWhere)) {
                $whereArray[] = "`$key`='$value'"; 

            }
            
            if ($key == "id") {
                $whereArray[] = "`$idname`=$value";
            }
            
            if (stripos($key, "where") !== false) {
                $whereArray[] = "$value";
            }
        }
        
        // WHERE
        $this->where = isset($whereArray) ? implode(" AND ", $whereArray) : null;
        
        // groupBy
        $this->groupBy = $queryParams['groupBy'] ?? null;
                
        // limit
        $this->limit = isset($queryParams['limit']) ? ($queryParams['limit'] !== "none" ? $queryParams['limit'] : null) : $this->limit; 
        
        // offset        
        $this->offset = $queryParams['offset'] ?? null;
        
        // properties
        if (isset($queryParams['properties'])) {
            $this->propertiesMerge($queryParams['properties']);
        }
    }
    
    public function field() 
    {
        return $this->fields;
    }
    
    public function where()
    {
        return $this->where;
    }
    
    public function groupBy()
    {
        return $this->groupBy;
    }
    
    public function orderBy() 
    {
        return $this->orderBy;
    }
    
    public function ordering()
    {
        return stripos($this->ordering,'asc') !== false ? "Ascending" : 
                ( stripos($this->ordering,"desc") !== false ? "Descending" : 
                (stripos($this->ordering, 'rand') !== false ? "Randomly" : "Unordering") );     
    }
    
    public function limit()
    {    
        return $this->limit;
    }

    public function offset()
    {
        return $this->offset;
    }
    
    public function getProperties()
    {
        return $this->properties;
    }
    
    private function propertiesMerge(string $propertiesIncrement) 
    {
        $propArray = explode(",", $propertiesIncrement);
                
        foreach ($propArray as $value) {
            $array[] = trim($value);
        }
        
        $this->properties = array_merge($this->properties, $array);
    }
    
}
