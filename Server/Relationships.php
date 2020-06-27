<?php

namespace Fwc\Api\Server;

class Relationships extends Crud
{
        
    public function getRelationship($tableOwner, $idOwner, $tableHas) 
    {
        $tableRel = $tableOwner.'_has_'.$tableHas;
        $idOwnerName = 'id'.$tableOwner;
        $idHasName = 'id'.$tableHas;
        
        $query = "SELECT * FROM $tableHas LEFT JOIN $tableRel ON $tableHas.$idHasName=$tableRel.$idHasName AND $tableRel.$idOwnerName=$idOwner;";
        
        return parent::getQuery($query);
    }
    
    public function putRelationship($tableOwner, $idOwner, $tableHas, $idHas) 
    { 
        $this->table = $tableOwner.'_has_'.$tableHas;
        
        $idOwnerName = 'id'.$tableOwner;
        $idHasName = 'id'.$tableHas;
        
        $ifExists = parent::read("COUNT(*) as q", "`$idOwnerName`=$idOwner AND `$idHasName`=$idHas");
                
        if (empty($ifExists)) {
            return parent::created([ $idOwnerName => $idOwner, $idHasName => $idHas ]);
        } 
    }
}
