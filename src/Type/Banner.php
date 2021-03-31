<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Banner extends Entity implements TypeInterface {
    protected $table = "banner";
    protected $type = "Banner";
    protected $properties = [ "*","image" ];
    protected $hasTypes = [ "image" => "ImageObject" ];

    public function put(array $params): array {
        unset($params['idadvertising']);
        return parent::put($params);
    }

    public function delete(array $params): array {
        return parent::delete([ "idbanner" => $params['idbanner'] ]);
    }

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("Banner");
    }
}
