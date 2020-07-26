<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;

class Person extends Entity implements TypeInterface
{
    protected $table = "person";
    protected $type = "Person";
    
    protected $properties = [ "*" ];
    
    protected $propertiesHasTypes = [ "address" => 'PostalAddress', "contactPoint" => "ContactPoint" ];


    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    public function post(array $params): array 
    {
        if(isset($params['givenName']) && isset($params['familyName'])) {
            $params['name'] = $params['givenName']." ".$params['familyName'];
            $params['dateRegistration'] = date('Y-m-d');
            return parent::post($params);
            
        } else {
            return [ "messagen" => "incomplete mandatory data" ];
        } 
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
    
    
    public function delete(array $params): array 
    {
        return parent::delete($params);        
    }
    
    /**
     * Create table in sql driver
     * @param type $type
     * @return bool
     */
    public function createSqlTable($type = null) 
    {         
        $maintenance = new Maintenance($this->request);
        
        $message[] = $maintenance->createSqlTable("PostalAddress");
        
        $message[] = $maintenance->createSqlTable("ContactPoint"); 
        
        $message[] = $maintenance->createSqlTable("ImageObject");
        
        $message[] = parent::createSqlTable("Person");
        
        return $message;
    }
}
