<?php

namespace Fwc\Api\Type;

/**
 * ContactPoint
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ContactPoint extends TypeAbstract implements TypeInterface
{
    protected $table = 'contactPoint';
    
    protected $type = 'ContactPoint';
    
    protected $properties = [ "telephone", "email" ];


    public function get(array $params): array
    {
        return parent::get($params);
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put(string $id, $params): array 
    {
        return parent::put($id, $params);
    }
    
    public function delete(string $id): array 
    {
        return parent::delete($id);
    }
    
    public function createSqlTable($type = null)
    {
        return parent::createSqlTable("ContactPoint");
    }
}
