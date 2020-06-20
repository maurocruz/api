<?php

namespace fwc\Thing;

/**
 * ImageObjectModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class ImageObjectModel extends Crud {
    public $table = "imageObject";
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getImagesHasTable($tableOwner, $idOwner, $order = null) {
        $tableHas = $tableOwner.'_has_'.$this->table;        
        $query = "SELECT $this->table.*, $tableHas.* "
                . "FROM $this->table, $tableHas "
                . "WHERE {$this->table}.id{$this->table}=$tableHas.id{$this->table} AND $tableHas.id{$tableOwner}=$idOwner";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";                
        return parent::getQuery($query);
    }
        
    public function getPrimaryImageOfTableOwner($tableOwner, $idOwner) {
        $tableHas = $tableOwner."_has_".$this->table;
        $query = "SELECT * FROM $this->table, $tableHas WHERE $tableHas.id{$this->table} = $this->table.id{$this->table} AND $tableHas.id{$tableOwner} = $idOwner AND if(representativeOfPage=1, representativeOfPage=1, representativeOfPage is null) LIMIT 1;";
        return parent::getQuery($query);
    }

    public function updateTableHas($tableOwner, $idOwner, $data, $idimageObject) {
        $this->table = $tableOwner."_has_imageObject";        
        return parent::update($data, "`id{$tableOwner}`=$idOwner AND `idimageObject`=$idimageObject");
    }
    
    public function deleteImageWithPartOf($tableOwner,$idOwner, $idimageObject) {
        return parent::deleteWithPartOf($tableOwner, $idOwner, 'imageObject', $idimageObject);
    }
    
    // OBTÉM OS GRUPOS DE IMAGENS
    public function getKeywords($group = null) {
        if ($group) {
            $query = "SELECT * FROM $this->table WHERE imageObject.keywords LIKE '$group';";       
        } else {
            $query = "SELECT DISTINCT(imageObject.keywords) FROM $this->table ORDER BY imageObject.keywords;";
        }
        return parent::getQuery($query);
    }
}
