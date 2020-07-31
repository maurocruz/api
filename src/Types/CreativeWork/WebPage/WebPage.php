<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

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
     * @param string $id
     * @param type $params
     * @return array
     */
    public function put(array $params): array 
    {
        return parent::put($params);
    }
    
    /**
     * DELETE
     * @param string $id
     * @param type $params
     * @return array
     */
    public function delete(array $params): array 
    {
        return parent::delete($params);
    }
    
    /**
     * CREATE SQL
     * @param type $type
     * @return type
     */    
    public function createSqlTable($type = null) 
    {
        return parent::createSqlTable("WebPage");
    }
}
