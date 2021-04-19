<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\Entity;
use Plinct\Cms\View\Types\Product\ProductView;

class Organization extends Entity implements TypeInterface {
    protected $table = "organization";
    protected $type = "Organization";
    protected $properties = [ "name", "description", "legalName", "taxId" ];
    protected $hasTypes = [ "address" => "PostalAddress", "contactPoint" => "ContactPoint", "member" => "Person", "location" => "Place", "image" => "ImageObject", "localBusiness" => "LocalBusiness" ];


    public function get(array $params): array {
        // GET DATA ORGANIZATION
        $data = parent::get($params);
        // GET OFFERS
        if (strpos($params['properties'], "hasOfferCatalog") !== false) {
            $offerCatalog = (new Offer())->get([ "format" => "ItemList", "offeredBy" => $params['id'], "offeredByType" => "Organization" ]);
            $data[0]['hasOfferCatalog'] = $offerCatalog;
        }
        return $data;
    }


    public function createSqlTable($type = null) : array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("ContactPoint");
        $message[] = $maintenance->createSqlTable("PostalAddress");
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = $maintenance->createSqlTable("Person");
        $message[] = $maintenance->createSqlTable("Place");
        $message[] = parent::createSqlTable("Organization");
        return $message;
    }
}
