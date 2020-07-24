<?php

namespace Plinct\Api\Server;

class Relationships extends Crud
{
    protected $tableHasPart;
    
    protected $idTableHasPart;
    
    protected $tableIsPartOf;
    
    protected $idTableIsPartOf;
    
    public function createNewEntityAndNewRelationship($param) 
    {
                
    }
    
    public function createRelationship($param) 
    {
        
    }
    
    public function updateRelationship($param)
    {
        
    }
    
    public function deleteRelationship($param) 
    {
        
    }
    
    public function getRelationship($tableOwner, $idOwner, $tableIsPartOf) 
    {
        $tableRel = $tableOwner.'_has_'.$tableIsPartOf;
        $idOwnerName = 'id'.$tableOwner;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        
        $query = "SELECT * FROM $tableIsPartOf, $tableRel WHERE $tableIsPartOf.$idIsPartOfName=$tableRel.$idIsPartOfName AND $tableRel.$idOwnerName=$idOwner";
        
        $query .= $tableIsPartOf == "imageObject" ? " ORDER BY position ASC" : null;
        
        $query .= ";";
        
        return parent::getQuery($query);
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
