<?php

namespace Plinct\Api\Type;

use Plinct\Api\Auth\SessionUser;
use Plinct\Api\Server\Entity;
use ReflectionException;

class Banner extends Entity implements TypeInterface
{
    protected $table = "banner";
    
    protected $type = "Banner";
    
    protected $properties = [ "*","image" ];
    
    protected $hasTypes = [ "image" => "ImageObject" ];

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
        unset($params['idadvertising']);
        return parent::put($params);
    }
    
    /**
     * DELETE
     * @param array $params
     * @return array
     */
    public function delete(array $params): array 
    {
        return parent::delete([ "idbanner" => $params['idbanner'] ]);
    }

    /**
     * CREATE SQL
     * @param string|null $type
     * @return array|string[]
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {        
        return parent::createSqlTable("Banner");
    }
}
