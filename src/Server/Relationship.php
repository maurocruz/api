<?php

namespace Plinct\Api\Server;

class Relationship extends Crud
{
    protected $tableHasPart;
    
    protected $idHasPart;
        
    protected $tableIsPartOf;
    
    protected $idIsPartOf;
    
    protected $table_has_table;
    
    protected $params;
    
    protected $table;
    
    protected $type;
    
    protected $properties;
    
    protected $hasTypes;

    public function setVars($params) 
    {
        if ($params['tableHasPart']) { 
            $this->tableHasPart = $params['tableHasPart'] ?? null;
        
            $this->idHasPart = $params['idHasPart'] ?? null;

            $this->tableIsPartOf = $params['tableIsPartOf'] ?? $this->table;

            $this->idIsPartOf = $params['idIsPartOf'] ?? $params['id'] ?? null;

            $this->table_has_table = $this->tableHasPart.'_has_'.$this->tableIsPartOf;

            unset($params['tableHasPart']);

            unset($params['idHasPart']);

            unset($params['tableIsPartOf']);

            unset($params['idIsPartOf']);
            
            unset($params['id']);
        }
        
        $this->params = $params;
    }
    
    public function getRelationship($tableHasPart, $idHasPart, $tableIsPartOf, $params = null)
    {        
        $filterget = new FilterGet($params, $this->table, $this->properties ?? []);
        
        $orderBy = $filterget->orderBy();
        
        $tableRel = $tableHasPart.'_has_'.$tableIsPartOf;
        $idHasPartName = 'id'.$tableHasPart;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        
        $query = "SELECT * FROM $tableIsPartOf, $tableRel WHERE $tableIsPartOf.$idIsPartOfName=$tableRel.$idIsPartOfName AND $tableRel.$idHasPartName=$idHasPart";
        // representativeOfPage
        $query .= in_array('representativeOfPage',$this->properties) ? " AND `representativeOfPage` IS TRUE " : null;
        //
        $query .= $tableIsPartOf == "imageObject" ? " ORDER BY position ASC" : ($orderBy ? " ORDER BY $orderBy" : null);
        
        $query .= ";";
        
        return PDOConnect::run($query);
    }
    
    public function postRelationship(array $params): array
    {
        $this->setVars($params);
        
        // created is part of
        if (isset($params['id'])) {
            $this->idIsPartOf = $params['id'];
        } else {
            parent::created($this->params);
            $this->idIsPartOf = parent::lastInsertId();
        }
                
        // one to one relationship type 
        $propertyIsPartOf = $this->propertyIsPartOf();
        
        if ($propertyIsPartOf) {            
            // update has part
            $this->table = $this->tableHasPart;            
            parent::update([ $propertyIsPartOf => $this->idIsPartOf ], "`id{$this->tableHasPart}`={$this->idHasPart}");
        } 
        
        // with manys relationship type
        else {                         
            $idHasPartName = 'id'.$this->tableHasPart;
            $idIsPartOfName = 'id'.$this->tableIsPartOf;
                
            // many to many
            if (self::table_exists($this->table_has_table)) {
                $this->table = $this->table_has_table;
                
                $paramCreate = [ $idHasPartName => $this->idHasPart, $idIsPartOfName => $this->idIsPartOf ];            

               return parent::created($paramCreate);
            } 
            
            // many to many
            else {
                $this->table = $this->tableIsPartOf;
                
                $params[$idHasPartName] = $params['idHasPart'];
                
                unset($params['tableHasPart']);
                unset($params['idHasPart']);
                
                return parent::created($params);
            }
        }
        
        return $this->params;
    }
    
    public function putRelationship($params) 
    {               
        $this->setVars($params);
        
        $this->table = $this->table_has_table;
        
        $idHasPartName = 'id'.$this->tableHasPart;
        $idIsPartOfName = 'id'.$this->tableIsPartOf;
        
        $where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";            
        
        return parent::update($this->params, $where);
    }

    public function deleteRelationship($params)
    {      
        $this->setVars($params);
        
        $this->table = $this->table_has_table;
        
        $idHasPartName = 'id'.$this->tableHasPart;
        $idIsPartOfName = 'id'.$this->tableIsPartOf;
        
        $where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";  
                
        return parent::erase($where);
    }
    
    public function getHasTypes()
    {
        return $this->hasTypes;
    }       
    
    private static function getTypeObject($type) 
    {
        $classname = "\\Plinct\\Api\\Type\\". ucfirst($type);
        
        if (class_exists($classname)) {
            return new $classname();
            
        } else {
            return false;
        }
    }
    
    private static function table_exists($table)
    {
        return empty(PDOConnect::run("SHOW tables like '$table';")) ? false : true;    
    }
    
    private function propertyIsPartOf() 
    {        
        // check which properties exists in table
        $columns = parent::getQuery("SHOW COLUMNS FROM $this->tableHasPart");
                
        // fields columns bd        
        foreach ($columns as $valueColumns) {
            $propColumns[] = $valueColumns['Field'];
        }
        
        // has types of table has part
        $hasTypes = (self::getTypeObject($this->tableHasPart))->getHasTypes();
        
        // property is part type 
        $propIsPartType = array_keys($hasTypes, $this->type);
        
        return in_array($propIsPartType[0], $propColumns) ? $propIsPartType[0] : null;
        
    }
    
    protected function relationshipsInSchema($valueData, $valueProperty) 
    {
        $typeIsPartOf = $this->hasTypes[$valueProperty];
        
        $this->tableIsPartOf = lcfirst($typeIsPartOf);
        
        $typeIsPartOfObject = self::getTypeObject($typeIsPartOf);
        
        if ($typeIsPartOfObject) {

            // one to one
            if (array_key_exists($valueProperty, $valueData)) {

                if (is_numeric($this->idHasPart)) {
                    $resp = $typeIsPartOfObject->get([ "id" => $valueData[$valueProperty] ]);
                    return $resp[0] ?? null;
                } else {
                    return null;
                }
            }

            // manys
            else {
                $this->table_has_table = $this->tableHasPart."_has_".$this->tableIsPartOf;

                // many to many
                if (self::table_exists($this->table_has_table)) {
                    $rel = $this->getRelationship($this->table, $this->idHasPart, $this->tableIsPartOf);

                    foreach ($rel as $valueRel) {
                       $data[] = $typeIsPartOfObject->schema($valueRel);
                    }

                    return $data ?? null;
                }
                // one to many
                else {
                    $idTableHasPartName = "id".$this->tableHasPart;
                    return $typeIsPartOfObject->get([ $idTableHasPartName => $this->idHasPart ]);
                }
            }
        }
        return false;
    }
}
