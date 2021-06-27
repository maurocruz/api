<?php
namespace Plinct\Api\Server\Schema;

class Schema extends SchemaTrait {

    public function __construct($type, $properties, $hasTypes) {
        $this->type = $type;
        $this->properties = $properties;
        $this->hasTypes = $hasTypes;
    }

    public function buildSchema(array $params,array $data): array {
        $this->tableHasPart = lcfirst($this->type);
        $this->params = $params;
        $paramsProperties = $this->params['properties'] ?? null;
        // SET PROPERTIES and HAS TYPES
        if ($paramsProperties) {
            parent::setProperties($paramsProperties);
        }
        parent::setHasTypes();
        // IF LIST
        if (isset($params['format']) && $params['format'] == 'ItemList') {
            parent::listSchema($data);
        } else {
            foreach ($data as $value) {
                $this->schema[] = parent::newSchema($value);
            }
        }
        return $this->schema;
    }
}