<?php
namespace fwc\Thing;
/**
 * TravelAgencyPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class TravelAgencyPost extends ModelPost {
    protected $table = "localBusiness";

    public function add(): string {
        $id = parent::createNewAndReturnLastId($this->POST);
        return "/admin/modules/travelAgency/edit/$id";
    }
    
    public function edit(): bool {
        
    }
    
    public function erase() {
        
    }
    
    public function installSqlTable($dir = __DIR__) {
        parent::installSqlTable($dir);
        return true;
    }
}
