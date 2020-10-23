<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Product extends Entity implements TypeInterface
{
    protected $table = "product";

    protected $type = "Product";

    protected $properties = [ "name" ];

    protected $hasTypes = [ "image" => "ImageObject" ];

    public function get(array $params): array
    {
        return parent::get($params);
    }

    public function post(array $params): array
    {
        $params['dateCreated'] = date("Y-m-d H:i:s");
        return parent::post($params);
    }

    public function put(array $params): array
    {
        return parent::put($params);
    }

    public function delete(array $params): array
    {
        return parent::delete($params);
    }

    public function createSqlTable($type = null)
    {
        return $this->createSqlTable("product");
    }
}