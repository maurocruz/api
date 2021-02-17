<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class ContactPoint extends Entity implements TypeInterface
{
    protected $table = 'contactPoint';
    protected $type = 'ContactPoint';
    protected $properties = [ "name", "telephone", "email", "whatsapp", "contactType", "position", "obs" ];

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
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        return parent::createSqlTable("ContactPoint");
    }
}
