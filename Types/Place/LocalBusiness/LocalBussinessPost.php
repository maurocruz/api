<?php
namespace fwc\Thing;
/**
 * LOCAL BUSINESS
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class LocalBusinessPost extends ModelPost {
    public $table = "localBusiness";
    
    public function add() {
        // if place exists
        if (isset($this->POST['idplace'])) {
            $data = [ "location" => $this->POST['idplace'] ];
            $idlocalBusiness = parent::createNewAndReturnLastId($data);
        } else { // if not
            // create place
            $this->table = "place";
            $data['name'] = $this->POST['location'];
            $data['latitude'] = strstr($this->POST['geo'], ",", true);
            $data['longitude'] = trim(substr(strstr($this->POST['geo'], ","),1));
            $data['elevation'] = $this->POST['elevation'];
            $data['additionalType'] = $this->POST['additionalType'];            
            $location = parent::createNewAndReturnLastId($data);
            // create localbusines
            $this->table = "localBusiness";
            unset($data['latitude']);
            unset($data['longitude']);
            unset($data['elevation']);
            $data['location'] = $location;
            $idlocalBusiness = parent::createNewAndReturnLastId($data);
        }
        return "/admin/localBusiness/edit/$idlocalBusiness";
    }
    
    public function edit(): bool {
        $this->POST['description'] = addslashes($this->POST['description']);
        parent::updateById("idlocalBusiness");
        return true;        
    }
    
    public function erase() {
        parent::delete([ "idlocalBusiness" => $this->POST['idlocalBusiness'] ]);
        return "/admin/localBusiness";
    }
    
    public function selectOrganization() {
        parent::update([ "organization" => $this->POST['idorganization'] ], "idlocalBusiness=".$this->idOwner);
        return true;
    }
    
    public function createSqlTable($type = null) {        
        // require
        $maintenance = (new \fwc\Maintenance\Maintenance($this->settings));
        $maintenance->createSqlTable("ContactPoint");
        $maintenance->createSqlTable("PostalAddress");
        $maintenance->createSqlTable("ImageObject");
        $maintenance->createSqlTable("Person");
        $maintenance->createSqlTable("Place");
        $maintenance->createSqlTable("Organization");
        // sql create statement
        return parent::createSqlTable("LocalBusiness");
    }
}
