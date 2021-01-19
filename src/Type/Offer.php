<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Offer extends Entity implements TypeInterface
{
    protected $table = "offer";

    protected $type = "Offer";

    protected $properties = [ "*", "itemOffered" ];

    protected $hasTypes = [ "itemOffered" => true ];

    public function get(array $params): array
    {
        return parent::get($params);
    }

    public function post(array $params): array
    {
       return parent::post($params);
    }

    public function put(array $params): array
    {
        return parent::put($params);
    }

    public function delete(array $params): array
    {
        return parent::delete($params);
    }

    public function addInOrder($params): array
    {
        $itemOfferedTypeClassName = "\\Plinct\\Api\\Type\\".ucfirst($params['itemOfferedType']);
        var_dump($params['itemOffered']);
        var_dump($params['itemOfferedType']);
        if (class_exists($itemOfferedTypeClassName)) {
            $itemOfferedTypeClass = new $itemOfferedTypeClassName();
            $itemOfferedTypeData = $itemOfferedTypeClass->get([ "id" => $params['itemOffered'], "properties" => "offers"]);
            var_dump($itemOfferedTypeData[0]['offers']);
            $params['idIsPartOf'] = $itemOfferedTypeData[0]['offers']['idoffer'];
        }

        return parent::postRelationship($params);
    }

    public function createSqlTable($type = null) : array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("QuantitativeValue");

        // sql create statement
        $message[] = parent::createSqlTable("Offer");

        return $message;
    }
}