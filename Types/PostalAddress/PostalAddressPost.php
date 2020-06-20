<?php

namespace fwc\Thing;

/**
 * PostalAddressPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Cms\Helper\ClassFactory;

class PostalAddressPost extends ModelPost {
    protected $table = "postalAddress";

    public function add() {
        $idName = 'id'.$this->tableOwner;
        
        if (isset($this->POST['idpostalAddress']) || isset($this->POST['fwc_id'])) {
            $idPostalAddress = $this->POST['idpostalAddress'] ?? $this->POST['fwc_id'];
            (ClassFactory::createFwcThingClass($this->tableOwner, $this->settings, "Model"))->update([ "address" => $idPostalAddress ], "$idName=$this->idOwner");
            return true;
        } else {            
            $idPostalAddress = parent::createNewAndReturnLastId($this->POST);
            // update table owner
            $tableOwnerName = "fwc\\Thing\\".ucfirst($this->tableOwner)."Model";
            (new $tableOwnerName($this->settings))->update([ "address" => $idPostalAddress ], "$idName=$this->idOwner");
            return "/admin/".$this->tableOwner."/edit/".$this->idOwner;
        }
    }
    
    public function edit(): bool 
    {
        if (isset($this->POST['fwc_id'])) {
            $idName = 'id'.$this->tableOwner;
            (ClassFactory::createFwcThingClass($this->tableOwner, $this->settings, "Model"))->update([ "address" => $this->POST['fwc_id'] ], "`$idName`=$this->idOwner");
        } else {
            $idpostalAddress = $this->POST['idpostalAddress'];
            unset($this->POST['idpostalAddress']);
            parent::update($this->POST, "idpostalAddress=$idpostalAddress");
        }
        return true;
    }
    
    public function erase() {
        if ($this->tableOwner) {
            $idName = 'id'.$this->tableOwner;
            (ClassFactory::createFwcThingClass($this->tableOwner, $this->settings, "Model"))->update([ "address" => null ], "$idName=$this->idOwner" );
            return true;
        }
    }
            
    public function createSqlTable($type = null) {
        $sqlFile = __DIR__."/createSqlTable.sql";
        return parent::createSqlTable("PostalAddress");
    }
}
