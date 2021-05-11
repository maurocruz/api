<?php
namespace Plinct\Api\Server;

use Plinct\PDO\PDOConnect;

class Schema {
    private $schema;
    private $context = "https://schema.org";
    private $type;
    private $properties;
    private $data = [];

    public function __construct($type) {
        $this->type = $type;
    }

    public function setProperties($properties): void {
        $this->properties = $properties;
    }

    public function setData(array $params) {
        $filterGet = new FilterGet($params, lcfirst($this->type), $this->properties);
        $this->data = PDOConnect::run($filterGet->getSqlStatement());
    }

    public function setIdentifier(array $item): void {
        $this->schema['identifier'][] = $item;
    }
    public function addProperty($property, $value) {
        $this->schema[$property] = $value;
    }

    public function responseSingle(): array {
        $this->schema['@context'] = $this->context;
        $this->schema['@type'] = $this->type;
        if (isset($this->data[0])) {
            foreach ($this->data[0] as $key => $value) {
                $this->schema[$key] = $value;
            }
        }
        return $this->ready();
    }

    public function responseMultiple() {
        foreach ($this->data as $key => $value) {
            $this->schema[][$key] = $value;
        }
        return $this->schema;
    }

    public function ready(): array {
        $this->schema['@context'] = $this->context;
        $this->schema['@type'] = $this->type;
        return $this->schema;
    }
}