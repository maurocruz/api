<?php
namespace Plinct\Api\Server;

class Schema {
    private $schema = [];

    public function __construct($type) {
        $this->setSchema($type);
    }

    public function setSchema($type) {
        $this->schema = [ "@context" => "https://schema.org", "@type" => $type ];
    }

    public function addProperty($property, $value) {
        $this->schema[$property] = $value;
    }

    public function getSchema(): array {
        return $this->schema;
    }
}