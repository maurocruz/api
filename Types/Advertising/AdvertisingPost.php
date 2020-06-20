<?php
namespace fwc\Thing;

/**
 * AdvertsPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class AdvertisingPost extends ModelPost {
    public $table = "advertising";
    
    public function add()
    {
        // add advertising
        $idadvertising = parent::createNewAndReturnLastId($this->POST);
        // update history
        (new HistoryModel())->setHistory("UPDATE", filter_input(INPUT_GET, 'summary') ?? "New advertising", $idadvertising, "advertising" );  
        return "/admin/advertising/edit/$idadvertising";
    }
    
    public function edit(): bool 
    {
        $idadvertising = $this->POST['idadvertising'];
        unset($this->POST['idadvertising']);        
        // update contracts
        (new AdvertisingModel())->update($this->POST, "idadvertising=$idadvertising");
        // update history
        (new HistoryModel())->setHistory("UPDATE", filter_input(INPUT_GET, 'summary'), $this->idOwner, $this->tableOwner);        
        return true;
    }
    
    public function erase() 
    {
        $idadvertising = $this->POST['idadvertising'];
        parent::delete([ "idadvertising" => $idadvertising ]);
        return "/admin/advertising";
    }
}
