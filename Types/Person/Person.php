<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class Person extends TypeAbstract implements TypeInterface
{
    protected $table = "person";
    protected $type = "Person";
    
    protected $properties = [ "name", "givenName", "familyName", "url" ];
    
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
    public function put(string $id, $params): array
    {
        return parent::put($id, $params);
    }  
    
    
    public function delete(string $id, $params): array 
    {
        return parent::delete($id, $params);        
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
