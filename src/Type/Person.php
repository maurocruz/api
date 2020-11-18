<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class Person extends Entity implements TypeInterface
{
    protected $table = "person";
    
    protected $type = "Person";
    
    protected $properties = [ "name" ];
    
    protected $hasTypes = [ "address" => 'PostalAddress', "contactPoint" => "ContactPoint" ];

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
     * Create table in sql driver
     * @param $type
     * @return bool
     * @throws ReflectionException
     */
    public function createSqlTable($type = null) 
    {         
        $maintenance = new Maintenance();
        
        $message[] = $maintenance->createSqlTable("PostalAddress");        
        $message[] = $maintenance->createSqlTable("ContactPoint");         
        $message[] = $maintenance->createSqlTable("ImageObject");
        
        $message[] = parent::createSqlTable("Person");
        
        return $message;
    }
}
