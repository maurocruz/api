<?php

namespace fwc\Thing;

/**
 * TaxonModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class TaxonModel extends Crud {
    public $table = "herbario";

    public function __construct() {
        parent::__construct();
    }
    
    public function getAll(){
        $query = "SELECT * FROM $this->table ORDER BY family, genero;";
        return parent::getQuery($query);
    }
    
    public function getAllFamilies() {
        $query = "SELECT distinct(family) FROM $this->table ORDER BY family;";
        return parent::getQuery($query);
    }
    
    public function getAllGenusByFamily($familyName) {        
        $query = "SELECT distinct(genero) FROM $this->table WHERE family='$familyName' ORDER BY genero;";
        return parent::getQuery($query);
    }
    
    public function getAllSpeciesByFamily($family) {
        $query = "SELECT * FROM $this->table WHERE herbario.family='$family' ORDER BY nome;";
        return parent::getQuery($query);
    }
    
    public function getSpecie($family, $genus, $specie = null) {
        $query = "SELECT * FROM herbario WHERE family='$family' AND genero='$genus'";
        $query .= $specie ? " AND especie='$specie'" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getSpecieFromGenus($family, $genus) {
        $query = "SELECT * FROM herbario WHERE family='$family' AND genero='$genus';";
        return parent::getQuery($query);
    }
    
    public function getImagesFromSpecie($idtaxon) {
        $query = "SELECT * FROM images, herbario_has_images WHERE herbario_has_images.idimages=images.idimages AND herbario_has_images.idherbario=54 ORDER BY herbario_has_images.position;";
    }
}
