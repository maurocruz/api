<?php
namespace fwc\Thing;
/**
 * ServicePost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ServicePost extends ModelPost {
    protected $table = "service";

    public function add(): string {
        $this->POST['providerId'] = $this->idOwner;
        $this->POST['providerType'] = $this->tableOwner;
        $isOfferCatalogOf = $this->POST['isOfferCatalogOf'] ?? null;
        //unset($this->POST['isOfferCatalogOf']);
        $idservice = parent::createNewAndReturnLastId($this->POST);
        /*if ($isOfferCatalogOf) {
            (new ServiceModel($this->settings))->insertWithHasOfferCatalog($idservice, $isOfferCatalogOf);
        }*/
        return "/admin/".$this->tableOwner."/edit/".$this->idOwner;
    }
        
    public function edit(): bool {
        parent::updateById();
        return true;        
    }
    
    public function erase() {
        $idservice = $this->POST['idservice'];
        parent::delete([ "idservice" => $idservice]);
        if ($this->tableOwner) {
            return true;
        } else {
            return "/admin/service";
        }
    }
    
    public function createSqlTable($type = null): bool {
        $maintenance = New \fwc\Maintenance\Maintenance($this->settings);
        $maintenance->createSqlTable("ImageObject");
        $maintenance->createSqlTable("Organization");
        $maintenance->createSqlTable("Person");
        $maintenance->createSqlTable("Place");
        $maintenance->createSqlTable("Offer");
        //
        return parent::createSqlTable("Service");
    }
}
