<?php
namespace fwc\Thing;
/**
 * TouristDestinationPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class TouristDestinationPost extends ModelPost {
    protected $table = "touristDestination";
    
    public function add(): string {
        $idplace = (new PlacePost($this->settings))->createNewAndReturnLastId($this->POST);
        $idtouristDestination = parent::createNewAndReturnLastId([ "location" => $idplace ]);
        return "/admin/touristDestination/edit/$idtouristDestination";        
    }
    
    public function edit(): bool {
        var_dump($this->POST);
        parent::updateById();
        return true;        
    }
    
    public function erase() {
        
    }
    
    public function createSqlTable($type = null): bool {
        // require
        $mantenance = (new \fwc\Maintenance\Maintenance($this->settings))->createSqlTable("Place");
        // statement
        return parent::createSqlTable("TouristDestination");
    }
}
