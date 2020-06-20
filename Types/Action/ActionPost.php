<?php
namespace fwc\Thing;
/**
 * ActionPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ActionPost  extends ModelPost {
    protected $table = "action";
    
    public function add() {
        $idtravelAction = parent::insertNewWithPartOf($this->tableOwner, $this->idOwner, $this->POST);
        return true;
    }
    
    public function edit(): bool {
        $this->POST['location'] = array_key_exists("location", $this->POST) && $this->POST['location'] !== '' ? $this->POST['location'] : null;
        parent::updateById();
        return true;
    }
    
    public function erase() {
        parent::delete([ "idaction" => $this->POST['idaction']]);
        return true;
    } 
}
