<?php

namespace Plinct\Api\Type;

class Thing extends TypeAbstract implements TypeInterface
{
    protected $table = "thing";
    protected $type = "Thing";

    public function get(): array
    {
        return parent::index();
    }
    
    // create (INSERT)
    public function post(array $args): array
    {      
        return $this->post($args);
    }
    
    // update (UPDATE)
    public function put(array $params): array
    {
        return $this->put($params);
    }
    
    // delete (DELETE)
    public function delete(string $id): array
    {
        return $this->delete($id);
    }
    
    public function createSqlTable($type = null)
    {
        return parent::createSqlTable('thing');
    }
}
