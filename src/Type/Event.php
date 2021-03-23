<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Event extends Entity implements TypeInterface {
    protected string $table = "event";
    protected string $type = "Event";
    protected array $properties = [ "name", "startDate" ];
    protected array $hasTypes = [ "location" => "Place", "image" => "ImageObject" ];

    public function post(array $params): array {
        $params['startDate'] = $params['startDate']." ".$params['startTime'];
        $params['endDate'] = $params['endDate']." ".$params['endTime'];
        unset($params['startTime']);
        unset($params['endTime']);
        return parent::post($params);
    }
    
    public function put(array $params): array {
        if (array_key_exists('startDate', $params)) {
            $params['startDate'] = $params['startDate'] . " " . $params['startTime'];
            unset($params['startTime']);
        }
        if (array_key_exists('endDate', $params)) {
            $params['endDate'] = $params['endDate'] . " " . $params['endTime'];
            unset($params['endTime']);
        }
        return parent::put($params);
    }
    
    public function delete(array $params): array {
        return parent::delete([ "idevent" => $params['id'] ]);
    }
    
    public function createSqlTable($type = null) : array {
        $maintenance = new Maintenance();
        $maintenance->createSqlTable("Person");        
        $maintenance->createSqlTable("ImageObject");        
        $maintenance->createSqlTable("Place");
        return parent::createSqlTable("Event");
    }    
}
