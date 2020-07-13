<?php

namespace Plinct\Api\Server;

class Relationships extends Crud
{
        
    public function getRelationship($tableOwner, $idOwner, $tableHas) 
    {
        $tableRel = $tableOwner.'_has_'.$tableHas;
        $idOwnerName = 'id'.$tableOwner;
        $idHasName = 'id'.$tableHas;
        
        $query = "SELECT * FROM $tableHas, $tableRel WHERE $tableHas.$idHasName=$tableRel.$idHasName AND $tableRel.$idOwnerName=$idOwner;";
        
        return parent::getQuery($query);
    }
    
    public function putRelationship($tableOwner, $idOwner, $tableHas, $idHas) 
    { 
        $this->table = $tableOwner.'_has_'.$tableHas;
        
        $idOwnerName = 'id'.$tableOwner;
        $idHasName = 'id'.$tableHas;
        
        $ifExists = parent::read("COUNT(*) as q", "`$idOwnerName`=$idOwner AND `$idHasName`=$idHas");
        
        if ($ifExists[0]['q'] == '0') {
            return parent::created([ $idOwnerName => $idOwner, $idHasName => $idHas ]);
            
        } else {
            return [ "messagem" => "Record relationship $tableHas already exists!" ];
        }
    }
    
    public function deleteRelationship($tableOwner, $idOwner, $tableHas, $idHas)
    {   
        $this->table = $tableOwner.'_has_'.$tableHas;
        
        $idOwnerName = 'id'.$tableOwner;
        $idHasName = 'id'.$tableHas;
        
        $ifExists = parent::read("COUNT(*) as q", "`$idOwnerName`=$idOwner AND `$idHasName`=$idHas");
                
        if (empty($ifExists)) {
            return [ "messagem" => "Relationship $tableHas doesn't exists!" ];
            
        } else {
            return parent::erase([ $idOwnerName => $idOwner, $idHasName => $idHas ]);
        }
    }
}
