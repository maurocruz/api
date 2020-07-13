<?php
namespace fwc\Thing;
/**
 * OfferPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class OfferPost extends ModelPost {
    protected $table = "offer";
    
    public function add(): string {        
        $this->POST['validThrough'] = $this->POST['validThrough'] == '' ? null : $this->POST['validThrough'];
        $this->POST['itemOfferedId'] = $this->idOwner;
        $this->POST['itemOfferedType'] = $this->tableOwner;
        $idoffer = parent::insertNewWithPartOf($this->tableOwner, $this->idOwner, $this->POST);
        return "/admin/offer/edit/$idoffer?".$this->tableOwner."=".$this->idOwner;
    }
    
    public function edit(): bool {      
        $this->POST['validThrough'] = $this->POST['validThrough'] == '' ? null : $this->POST['validThrough'];
        parent::updateById();
        return true;
    }
    
    public function erase() {
        parent::delete([ "idoffer" => $this->POST['idoffer'] ]);
        if ($this->tableOwner) {
            return "/admin/".$this->tableOwner."/edit/".$this->idOwner;
        } else {
            return "/admin/offer";
        }
    }
    
    public function addHasTable()
    {
        parent::insertWithHasPart($this->tableOwner, $this->idOwner, $this->POST['tableHas'], $this->POST['offerId']);
        return true;
    }


    public function createSqlTable($type = null): bool {
        $maintenance = new \fwc\Maintenance\Maintenance($this->settings);
        return parent::createSqlTable("Offer");
    }
}
