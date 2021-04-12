<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Offer extends Entity implements TypeInterface {
    protected $table = "offer";
    protected $type = "Offer";
    protected $properties = [ "*" ];
    protected $hasTypes = [ "itemOffered" => true ];

    public function addInOrder($params): array {
        $itemOfferedTypeClassName = "\\Plinct\\Api\\Type\\".ucfirst($params['itemOfferedType']);
        if (class_exists($itemOfferedTypeClassName)) {
            $itemOfferedTypeClass = new $itemOfferedTypeClassName();
            $itemOfferedTypeData = $itemOfferedTypeClass->get([ "id" => $params['itemOffered'], "properties" => "offers"]);
            $params['idIsPartOf'] = $itemOfferedTypeData[0]['offers']['idoffer'];
        }
        return parent::postRelationship($params);
    }

    public function createSqlTable($type = null) : array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("QuantitativeValue");
        // sql create statement
        $message[] = parent::createSqlTable("Offer");
        return $message;
    }
}
