<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Maintenance;

class Person extends TypeAbstract implements TypeInterface
{
    protected $table = "person";
    protected $type = "Person";
    
    protected $properties = [ "name", "givenName", "familyName", "url" ];
    
    protected $propertiesHasTypes = [ "address" => 'PostalAddress', "contactPoint" => "contactPoint" ];


    public function get(): array 
    {
        return parent::get();
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
    
    public function delete(string $id): array 
    {
        return parent::delete(["idperson" => $id]);        
    }
    
    public function put(string $id): array
    {
        return parent::put($id);
    }
    
    /**
     * Create table in sql driver
     * @param type $type
     * @return bool
     */
    public function createSqlTable($type = null): bool 
    {         
        $maintenance = new Maintenance($this->request);        
        $maintenance->createSqlTable("PostalAddress");        
        $maintenance->createSqlTable("ContactPoint");        
        return parent::createSqlTable("Person");
    }
}
