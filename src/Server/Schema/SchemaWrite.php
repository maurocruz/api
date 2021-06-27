<?php
namespace Plinct\Api\Server\Schema;

class SchemaWrite {
    private $context;
    private $type;
    private $properties = [];
    private $schema = null;

    /**
     * SchemaWrite constructor.
     * @param $context
     * @param $type
     */
    public function __construct($context, $type) {
        $this->context = $context;
        $this->type = $type;
    }

    /**
     * @param mixed $property
     */
    public function addProperty(string $property, $value): void {
        if (array_key_exists($property,$this->properties) && is_array($this->properties[$property])) {
            $this->properties[$property][] = $value;
        } else {
            $this->properties[$property] = $value;
        }
    }

    /**
     * @return array|null
     */
    public function ready(): ?array {
        if (!empty($this->properties)) {
            $this->schema['@context'] = $this->context;
            $this->schema['@type'] = $this->type;
            foreach ($this->properties as $key => $value) {
                $this->schema[$key] = $value;
            }
        }
        return $this->schema;
    }
}