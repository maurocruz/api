<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Person extends Entity implements TypeInterface {
    protected $table = "person";
    protected $type = "Person";
    protected $properties = [ "name" ];
    protected $hasTypes = [ "address" => 'PostalAddress', "contactPoint" => "ContactPoint", "image" => "ImageObject" ];

    public function post(array $params): array {
        if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
            return parent::post($params);
        } elseif (isset($params['givenName']) && isset($params['familyName'])) {
            $params['name'] = $params['givenName']." ".$params['familyName'];
            $params['dateRegistration'] = date('Y-m-d');
            return parent::post($params);
        } else {
            return [ "message" => "incomplete mandatory data" ];
        } 
    }

    public function put(array $params): array {
        $params['name'] = $params['givenName']." ".$params['familyName'];
        return parent::put($params);
    }

    public function createSqlTable($type = null) : array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("PostalAddress");        
        $message[] = $maintenance->createSqlTable("ContactPoint");         
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] = parent::createSqlTable("Person");
        return $message;
    }
}
