<?php

/**
 * ModelGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace Fwc\Api\Type;

use Fwc\Api\Server\Crud;

class ModelGet extends Crud 
{
    protected $httpRoot; //deprecated used on imageObject
    
    public function __construct()
    {
        parent::__construct();
        $this->httpRoot = (filter_input(INPUT_SERVER, "REQUEST_SCHEME") ?? filter_input(INPUT_SERVER, "HTTP_X_FORWARDED_PROTO"))."://".filter_input(INPUT_SERVER, "HTTP_HOST");
    }    
    
    protected function index(string $where = null, $order = null, $groupBy = null, $limit = null, $offset = null)
    {
        $query = "SELECT * FROM $this->table";
        $query .= $where ? " WHERE $where" : null;
        $query .= $groupBy ? " GROUP BY $groupBy" : null;
        $query .= $order ? " ORDER BY $order" : null;
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= $offset ? " OFFSET $offset" : null;
        $query .= ";";
        return parent::getQuery($query);
    } 
    
    protected function listAll(string $where = null, $order = null, $limit = null, $offset = null)
    {
        $query = "SELECT * FROM $this->table";
        $query .= $where ? " WHERE $where" : null;
        $query .= $order ? " ORDER BY $order" : null;
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= $offset ? " OFFSET $offset" : null;
        $query .= ";";
        return parent::getQuery($query);
    }    

    protected function listAllWithPartOf(string $tableOwner, string $idOwner) 
    {
        $tableHas = $tableOwner."_has_".$this->table;
        $idOwnerName = "id".$tableOwner;
        $idHasName = "id".$this->table;
        $query = "SELECT * FROM $this->table, $tableHas WHERE $tableHas.$idOwnerName=? AND $this->table.$idHasName=$tableHas.$idHasName;";
        return parent::getQuery($query, [ $idOwner ]);
    }
    
    protected function selectById($id, $order = null, $field = '*') 
    {
        $where = "id{$this->table}=?";
        return parent::read($field, $where, $order, null, [ $id ]);
    }
    
    protected function selectByNameValue($name, $value, $field = "*", $order = null)
    {
        $where = "$name=?";
        return parent::read($field, $where, $order, null, [ "$value" ]);
    }

    protected function getHasPart($tableOwner, $idOwner, $groupby = null, $order = null)
    {
        $tableHas = $tableOwner."_has_".$this->table;
        $idTable = "id".$this->table;
        
        $where = "{$this->table}.{$idTable}={$tableHas}.{$idTable} AND {$tableHas}.id{$tableOwner}=$idOwner";
        
        $oldTable = $this->table;
        $this->table = $this->table.",".$tableHas;
        
        $stmt = parent::read("*", $where, $groupby, $order, [ $idOwner ]);
        
        $this->table = $oldTable;
        
        return $stmt;
    }
       
    public function selectByAjax($type, $idName, $queryParams) : string 
    {
        $data = $this->selectByName($queryParams['name']);        
        if (empty($data)) {
            $list = null;
        } else {
            foreach ($data as $value) {
                $list[] = [ "@type" => $type, "name" => $value['name'], "identifier" => $value[$idName] ];
            }
        }        
        $array = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "itemListElement" => $list
        ];        
        return json_encode($array);
    } 
    
    public function selectByName($words)
    {
        $query = "SELECT * FROM $this->table WHERE name LIKE '$words%' GROUP BY id{$this->table} ORDER BY name;";
        return parent::getQuery($query);
    }
    
    public function selectByUrl($url) 
    {
        $query = "SELECT * FROM $this->table WHERE `url`='$url';";
        return parent::getQuery($query);
    }
    
    public function selectNameByAjax($queryStrings)
    {
        $name = $queryStrings['name'];
        $data = parent::read("*", "name LIKE '%$name%'", null, "name");
        return $this->listNameAndId($data);
    }
    
    public function searchByHttpRequest($queryStrings)
    {
        $name = $queryStrings['name'];
        $value = $queryStrings['value'];
        $data =  parent::read("*", "`$name` LIKE '%$value%'", null, $name);
        return $this->listNameAndId($data, $name);
    }
    
    protected function listNameAndId($data, $name = "name")
    {
        $idTableName = "id".$this->table;
        if (empty($data)) {
            $list = null;
        } else {
            foreach ($data as $value) {
                $list[] = [ "@type" => ucfirst($this->table), "name" => $value[$name], "identifier" => $value[$idTableName] ];
            }
        }        
        return json_encode(ItemList::list(count($data), $list));
    }
}
