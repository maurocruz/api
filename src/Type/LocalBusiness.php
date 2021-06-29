<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Schema\Schema;

class LocalBusiness extends Entity implements TypeInterface {
    protected $table = "localBusiness";
    protected $type = "LocalBusiness";
    protected $properties = [ "name" ];
    protected $hasTypes = [ "location" => "Place", "organization" => "Organization", "contactPoint" => "ContactPoint", "address" => "PostalAddress", "member" => "Person", "image" => "ImageObject" ];

    public function post(array $params): array {
        $params['dateCreated'] = date("Y-m-d");
        return parent::post($params);
    }

    public function buildSchema($params, $data): array {
        return (new Schema($this->type, $this->properties, $this->hasTypes))->buildSchema($params, $data);
    }
}
