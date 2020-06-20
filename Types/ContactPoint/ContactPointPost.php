<?php
namespace fwc\Thing;
/**
 * ContactPointPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ContactPointPost extends ModelPost {
    public $table = "contactPoint";
    
    public function add(): string {
        parent::insertNewWithPartOf($this->tableOwner, $this->idOwner, $this->POST);
       return "/admin/".$this->tableOwner."/edit/".$this->idOwner;
    }
    
    public function edit(): bool {
        parent::updateById();        
        return true;
    }

    public function erase() {
        $id = $this->POST['identifier'];
        parent::delete([ "idcontactPoint" => $id ]);
        return true;
    }
    
    public function createSqlTable($type = null) {
        // sql create statement
        return parent::createSqlTable("ContactPoint");
    }
}
