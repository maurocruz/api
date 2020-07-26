<?php

namespace Plinct\Api\Server;

use Fwc\Api\Type\TypeInterface as Type;

class Relationship extends Crud
{
    protected $tableHasPart;
    
    protected $tableHasPartObject;
    
    protected $idHasPart;
    
    protected $tableIsPartOf;
    
    protected $idIsPartOf;
    
    protected $params;
    
    protected $type;
    
    protected $hasTypes;

    public function setVars($params) 
    {
        if ($params['tableHasPart']) { 
            $this->tableHasPart = $params['tableHasPart'] ?? null;

            $classname = "\\Plinct\\Api\\Type\\". ucfirst($params['tableHasPart']);
            $this->tableHasPartObject = new $classname();
        }
        
        $this->idHasPart = $params['idHasPart'] ?? null;
        
        $this->tableIsPartOf = $params['tableIsPartOf'] ?? null;
        
        $this->idIsPartOf = $params['idIsPartOf'] ?? null;
                
        unset($params['tableHasPart']);
        
        unset($params['idHasPart']);
        
        unset($params['tableIsPartOf']);
        
        unset($params['idIsPartOf']);
        
        $this->params = $params;
    }
    
    public function postRelationship(array $params): array
    {
        $this->setVars($params);
                
        // created is part of
        parent::created($this->params);
        $this->idIsPartOf = parent::lastInsertId();
                
        // one to one relationship type 
        $propertyIsPartOf = $this->propertyIsPartOf();
        
        if ($propertyIsPartOf) {            
            // update has part
            $this->table = $this->tableHasPart;            
            parent::update([ $propertyIsPartOf => $this->idIsPartOf ], "`id{$this->tableHasPart}`={$this->idHasPart}");
        } 
        
        // one to many relationship type
        else {             
            $this->table = $this->tableHasPart.'_has_'.$this->tableIsPartOf;

            $idHasPartName = 'id'.$this->tableHasPart;
            $idIsPartOfName = 'id'.$this->tableIsPartOf;

            $paramCreate = [ $idHasPartName => $this->idHasPart, $idIsPartOfName => $this->idIsPartOf ];            

            return parent::created($paramCreate);
        }
        
        return $this->params;
    }
    
    public function getHasTypes()
    {
        return $this->hasTypes;
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
        $hasTypes = $this->tableHasPartObject->getHasTypes();
        
        // property is part type 
        $propIsPartType = array_keys($hasTypes, $this->type);
        
        return in_array($propIsPartType[0], $propColumns) ? $propIsPartType[0] : null;
        
    }
    
    public function getRelationship($tableOwner, $idOwner, $tableIsPartOf) 
    {
        $tableRel = $tableOwner.'_has_'.$tableIsPartOf;
        $idOwnerName = 'id'.$tableOwner;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        
        $query = "SELECT * FROM $tableIsPartOf, $tableRel WHERE $tableIsPartOf.$idIsPartOfName=$tableRel.$idIsPartOfName AND $tableRel.$idOwnerName=$idOwner";
        
        $query .= $tableIsPartOf == "imageObject" ? " ORDER BY position ASC" : null;
        
        $query .= ";";
        
        return PDOConnect::run($query);
    }
    
    /*public function putRelationship($tableOwner, $idOwner, $tableIsPartOf, $idIsPartOf) 
    { 
        $this->table = $tableOwner.'_has_'.$tableIsPartOf;
        
        $idOwnerName = 'id'.$tableOwner;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        
        $ifExists = parent::read("COUNT(*) as q", "`$idOwnerName`=$idOwner AND `$idIsPartOfName`=$idIsPartOf");
        
        if ($ifExists[0]['q'] == '0') {
            return parent::created([ $idOwnerName => $idOwner, $idIsPartOfName => $idIsPartOf ]);
            
        } else {
            return [ "messagem" => "Record relationship $tableIsPartOf already exists!" ];
        }
    }
    
    public function deleteRelationship($tableOwner, $idOwner, $tableIsPartOf, $idIsPartOf)
    {   
        $this->table = $tableOwner.'_has_'.$tableIsPartOf;
        
        $idOwnerName = 'id'.$tableOwner;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        
        $ifExists = parent::read("COUNT(*) as q", "`$idOwnerName`=$idOwner AND `$idIsPartOfName`=$idIsPartOf");
                
        if (empty($ifExists)) {
            return [ "messagem" => "Relationship $tableIsPartOf doesn't exists!" ];
            
        } else {
            return parent::erase([ $idOwnerName => $idOwner, $idIsPartOfName => $idIsPartOf ]);
        }
    }*/
}
