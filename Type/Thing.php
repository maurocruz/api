<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Thing extends Entity implements TypeInterface
{
    protected $table = "thing";
    protected $type = "Thing";

    public function get(array $params): array
    {
        return parent::get($params);
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
    public function delete(array $params): array
    {
        return $this->delete($params);
    }
    
    public function createSqlTable($type = null)
    {
        return parent::createSqlTable('Thing');
    }
}
