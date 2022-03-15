<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use Plinct\Web\Debug\Debug;
use ReflectionException;

class Event extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected string $table = "event";
    /**
     * @var string
     */
    protected string $type = "Event";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "name", "startDate" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "location" => "Place", "image" => "ImageObject", "subEvent"=>'Event' ];

  /**
   * @param array $params
   * @return array
   */
  public function post(array $params): array
  {
	  if (isset($params['tableHasPart']) && isset($params['idHasPart']) && (isset($params['id']) || isset($params['idIsPartOf']))) {
		  return parent::post($params);

    } elseif (!isset($params['name']) || !isset($params['startDate']) || !isset($params['endDate'])) {
			return ['status'=>'fail','message'=>'Missing mandatory values!'];

		} else {
			$params['startDate'] = $params['startDate'] . " " . ($params['startTime'] ?? "00:00:00");
			$params['endDate'] = $params['endDate'] . " " . ($params['endTime'] ?? "00:00:00");
			unset($params['startTime']);
			unset($params['endTime']);
			return parent::post($params);
		}
  }

    /**
     * @param array $params
     * @return array
     */
    public function put(array $params): array
    {
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

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null) : array
    {
        $maintenance = new Maintenance();
        $maintenance->createSqlTable("Person");        
        $maintenance->createSqlTable("ImageObject");        
        $maintenance->createSqlTable("Place");
        return parent::createSqlTable("Event");
    }    
}
