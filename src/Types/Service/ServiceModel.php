<?php
namespace fwc\Thing;
/**
 * ServiceModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ServiceModel extends \fwc\Api\Crud {
    protected $table = "service";
    
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    public function listServicesOfProvider($providerType, $providerId) {
        return parent::read("*", "`providerId`= $providerId AND `providerType`='$providerType'");
    }
        
    public function insertWithHasOfferCatalog($idservice, $isOfferCatalogOf) {
        $query = "INSERT INTO service_has_offerCatalog (`idservice`,`isOfferCatalogOf`) VALUES ($idservice, $isOfferCatalogOf); ";
        return $this->connect->query($query);
    }
}
