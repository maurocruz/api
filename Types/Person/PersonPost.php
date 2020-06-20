<?php
namespace fwc\Thing;
/**
 * PersonPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class PersonPost extends ModelPost {
    protected $table = "person";
    
    public function add() {
        $this->POST['dateRegistration'] = date("Y-m-d");
        $this->POST['name'] = $this->POST['givenName'] . " " . $this->POST['familyName'];
        $id = parent::createNewAndReturnLastId($this->POST);
        return "/admin/person/edit/$id";
    }
    
    public function edit(): bool {
        $idperson = $this->POST['idperson'];
        $this->POST['name'] = $this->POST['givenName'] . " " . $this->POST['familyname'];
        unset($this->POST['idperson']);
        (new PersonModel($this->settings))->update($this->POST, "idperson=$idperson");
        return true;
    }
    
    public function erase() {
        $idperson = $this->POST['idperson'];
        parent::delete([ "idperson" => $idperson ]);
        return "/admin/person";
    }    
    
    public function addWithPartOf() {
        if (isset($this->POST['idperson'])) {
            parent::insertWithHasPart($this->tableOwner, $this->idOwner, "person", $this->POST['idperson']);
        } else {
            $this->POST['dateRegistration'] = date("Y-m-d H:i:s");
            parent::insertNewWithPartOf($this->tableOwner, $this->idOwner, $this->POST);
        }
        return true;
    }
    
    public function editWithPartOf() {
        (new PersonModel($this->settings))->updateWithPartOf($this->tableOwner, $this->idOwner, 'person', $this->POST['idperson'], ["jobTitle" => $this->POST['jobTitle']]);
        return true;
    }
    
    public function deleteWithPartOf() {
        (new PersonModel($this->settings))->deleteWithPartOf($this->tableOwner, $this->idOwner, 'person', $this->POST['idperson']);
        return true;
    }
    
    public function createSqlTable($type = null) {                  
        // require
        $maintenance = (new \fwc\Maintenance\Maintenance($this->settings))->createSqlTable("ContactPoint");
        // sql create statement
        return parent::createSqlTable("Person");
    }
}
