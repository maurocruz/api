<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class WebPage extends Entity implements TypeInterface
{
    protected $table = "webPage";
    
    protected $type = "WebPage";
    
    protected $properties = [ "name", "description", "url" ];
    
    protected $hasTypes = [ "hasPart" => "WebPageElement" ];

    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    /**
     * POST
     * @param array $params
     * @return array
     */
    public function post(array $params): array 
    {
        return parent::post($params);
    }

    /**
     * PUT
     * @param array $params
     * @return array
     */
    public function put(array $params): array 
    {
        return parent::put($params);
    }

    /**
     * DELETE
     * @param array $params
     * @return array
     */
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }

    /**
     * CREATE SQL
     * @param null $type $type
     * @return array|string[]
     * @throws ReflectionException
     */
    public function createSqlTable($type = null) 
    {
        $maintenance = new Maintenance();

        $message[] = $maintenance->createSqlTable("propertyValue");
        $message[] = parent::createSqlTable("webPage");
        $message[] = $maintenance->createSqlTable("webPageElement");

        return $message;
    }
}
