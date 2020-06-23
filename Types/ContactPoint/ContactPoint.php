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
    
    public function get(): array
    {
        return parent::get();
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put(string $id): array 
    {
        return parent::put($id);
    }
    
    public function delete(string $id): array 
    {
        return parent::delete($id);
    }
    
    public function createSqlTable($type = null): bool
    {
        return parent::createSqlTable("ContactPoint");
    }
}
