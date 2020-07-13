<?php
namespace fwc\Thing;
/**
 * TravelActionPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class TravelActionPost extends ModelPost {
    protected $table = "travelAction";
    
    public function add() {
        $this->POST['idaction'] = $this->idOwner;        
        parent::created($this->POST);
        return true;;
    }
    
    public function edit(): bool {
        parent::updateById();
        return true;
    }
    
    public function erase() {        
        parent::delete([ "idtravelAction" => $this->POST['idtravelAction']]);
        return true;
    }    
}
