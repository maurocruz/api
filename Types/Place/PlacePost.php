<?php
namespace fwc\Thing;
/**
 * PlaceEditing
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class PlacePost extends ModelPost {
    protected $table = "place";

    public function add() 
    { 
        $this->setLatitudeAndLongitudeFromGeo();
                    
        if (isset($this->POST['idplace'])) {
            unset($this->POST['idplace']);
        }

        $idplace = parent::createNewAndReturnLastId($this->POST);

        if ($this->tableOwner) {
            (new PlaceModel($this->settings))->addPlaceIntoTable($this->tableOwner, $this->idOwner, $idplace);
            return "/admin/".$this->tableOwner."/edit/".$this->idOwner;

        } else {
            return "/admin/place/edit/$idplace";
        }        
    }   
    
    public function addWithPart()
    {        
        $this->setLatitudeAndLongitudeFromGeo();        
        (new PlaceModel($this->settings))->addPlaceIntoTable($this->tableOwner, $this->idOwner, $this->POST['idplace']);
        return "/admin/".$this->tableOwner."/edit/".$this->idOwner;        
    }
    
    public function edit(): bool 
    {  
        $this->setLatitudeAndLongitudeFromGeo();
        
        $idplace = $this->POST['idplace'];
        unset($this->POST['idplace']);  
        
        // update place
        (new PlaceModel($this->settings))->update($this->POST, "idplace=$idplace");        
        return true;
    }
    
    public function erase() {
        parent::delete([ "idplace" => $this->POST['idplace'] ]);
        return "/admin/place";
    }
    
    private function setLatitudeAndLongitudeFromGeo() 
    {
        $this->POST['latitude'] = strstr($this->POST['geo'], ",", true);
        $this->POST['longitude'] = trim(substr(strstr($this->POST['geo'], ","),1));
        unset($this->POST['geo']);
    }
    
    public function eraseWithPart() 
    {
        $this->table = $this->tableOwner;
        parent::update([ "location" => null ], "idevent=".$this->idOwner);
        return true;
    }
    
    public function createNewAndReturnLastId($data): string {
        return parent::createNewAndReturnLastId($data);
    }    
    
    public function createSqlTable($type = null) 
    {
        // require        
        $maintenance = new \fwc\Maintenance\Maintenance($this->settings);
        // image object
        $maintenance->createSqlTable("ImageObject");
        // postal address
        $maintenance->createSqlTable("PostalAddress");        
        // sql create statement
        return parent::createSqlTable("Place");
    }
}
