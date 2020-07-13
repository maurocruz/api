<?php
namespace fwc\Thing;
/**
 * TouristAttractionPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class TouristAttractionPost extends ModelPost {
    protected $table = "touristAttraction";
    
    public function add(): string 
    {
        $idtouristattraction = parent::createNewAndReturnLastId([ "location" => $this->POST['location'] ]);
        return "/admin/touristAttraction/edit/$idtouristattraction";
    }
    
    public function edit(): bool {
        parent::updateById();
        return true;
    }
    
    public function erase() 
    {
        parent::delete([ "idtouristAttraction" => $this->POST['idtouristAttraction'] ]);
        return "/admin/touristAttraction";
    }
    
    public function createSqlTable($type = null) {
        $maintenance = new \fwc\Maintenance\Maintenance($this->settings);
        $maintenance->createSqlTable("Place");
        $maintenance->createSqlTable("TouristDestination");
        $maintenance->createSqlTable("LocalBusiness");
        // sql create statement
        return parent::createSqlTable("TouristAttraction");
    }
}

