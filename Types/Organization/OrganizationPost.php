<?php
namespace fwc\Thing;
/**
 * OrganizationPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class OrganizationPost extends ModelPost {
    protected $table = "organization";
    
    public function add(): string {
        $id = parent::createNewAndReturnLastId($this->POST);
        return "/admin/organization/edit/$id";
    }
    
    public function edit(): bool {
        parent::updateById('idOrganization');
        return true;
    }
    
    public function erase() {
        $idOrganization = $this->POST['idOrganization'];
        (new OrganizationModel($this->settings))->delete("idOrganization=$idOrganization");
        return "/admin/modules/organization";
    }
    
    public function addWithPartOf() {
        $idTableName = "id".$this->tableOwner;
        (\fwc\Cms\Helper\ClassFactory::createFwcThingClass($this->tableOwner, $this->settings, "Post"))->update($this->POST, "`$idTableName`=$this->idOwner");
        return true;
    }
    
    public function eraseWithPartOf() {
        $idTableName = "id".$this->tableOwner;
        (\fwc\Cms\Helper\ClassFactory::createFwcThingClass($this->tableOwner, $this->settings, "Post"))->update([ "organization" => null ] , "`$idTableName`=$this->idOwner");
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
        // sql create statement
        return parent::createSqlTable("Organization");
    }
}
