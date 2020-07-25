<?php

namespace Plinct\Api\Type;

class PostalAddress extends TypeAbstract implements TypeInterface
{
    protected $table = 'postalAddress';
    
    protected $type = 'PostalAddress';
    
    protected $properties = [ 'streetAddress', 'addressLocality', 'addressRegion', 'addressCountry', 'postalCode' ]; 
    
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
    
    public function postRelationship(array $params) 
    {
        return parent::postRelationship($params);
    }
    
    public function newAndPostRelationship(array $params) 
    {
        return parent::newAndPostRelationship($params);
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
        return parent::createSqlTable("PostalAddress");
    }
}
