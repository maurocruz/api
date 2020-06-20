<?php
namespace fwc\Thing;
/**
 * TripPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class TripPost extends ModelPost {
    protected $table = "trip";
    
    public function add() {
        $idtrip = parent::createNewAndReturnLastId($this->POST);
        return "/admin/trip/edit/$idtrip?providerId=".$this->POST['providerId'];
    }
    
    public function edit(): bool {
        parent::updateById();
        return true;
    }
    
    public function erase() {
        
    }
    
    public function addOffer() {
        parent::insertWithHasPart($this->tableOwner, $this->idOwner, "offer", $this->POST['idoffer'], [ "quantity" => $this->POST['quantity']]);
        return true;
    }


    public function editOffer() {        
        parent::editHasPart($this->tableOwner, $this->idOwner, "offer", $this->POST['idoffer'], [ "quantity" => $this->POST['quantity']]);
        return true;
    }
    
    public function createSqlTable($type = null): bool {
        $maintenance = New \fwc\Maintenance\Maintenance($this->settings);
        $maintenance->createSqlTable("Place");
        $maintenance->createSqlTable("LocalBusiness");
        $maintenance->createSqlTable("Offer");
        return parent::createSqlTable("Trip");
    }
}
