<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Offer extends Entity implements TypeInterface {
    protected string $table = "offer";
    protected string $type = "Offer";
    protected array $properties = [ "*" ];
    protected array $hasTypes = [ "itemOffered" => true ];

    public function post(array $params): array {
        unset($params['tableHasPart']);
        return parent::post($params);
    }

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
