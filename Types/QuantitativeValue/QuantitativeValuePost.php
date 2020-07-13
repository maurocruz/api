<?php
namespace fwc\Thing;
/**
 * QuantitativeValuePost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class QuantitativeValuePost extends ModelPost {
    protected $table = "quantitativeValue";
    
    public function add() {
        $property = $this->POST['property'];
        unset($this->POST['property']);
        // add table quantitativeValue
        $idQV = parent::createNewAndReturnLastId($this->POST);
        // update table offer
        $this->table = $this->tableOwner;
        parent::update([ $property => $idQV ], "idoffer=".$this->idOwner);
        // return
        return true;
    }
    
    public function edit(): bool {
        
    }
    
    public function erase() {
        
    }
}
