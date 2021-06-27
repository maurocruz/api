<?php
namespace Plinct\Api\Server\Schema;


class SchemaTrait extends  SchemaAbstract {
    
    protected function listSchema(array $data) {
        $listItem=[];
        $this->schema['@context'] = $this->context;
        $this->schema['@type'] = "ItemList";
        $this->schema['numberOfItems'] = count($data);
        $this->schema['itemListOrder'] = $this->params['ordering'] ?? 'ascending';
        if (empty($data)) {
            $listItem = [];
        } else {
            foreach ($data as $key => $value) {
                $item['@type'] = "ListItem";
                $item['position'] = ($key + 1);
                $item['item'] = self::newSchema($value);
                $listItem[] = $item;
            }
        }
        $this->schema["itemListElement"] = $listItem;
    }

    protected function newSchema(array $data): ?array {
        $this->idHasPart = $data['id'] ?? $data["id$this->tableHasPart"] ?? null;
        // SCHEMA WRITE
        $schema = new SchemaWrite($this->context, $this->type);
        // ADD SELECTED PROPERTIES
        foreach ($data as $property => $valueProperty) {
            // IF '*' with property
            if (array_search('*',$this->properties) !== false) {
                $schema->addProperty($property, $valueProperty);
            } else {
                if (array_search($property,$this->properties) !== false) {
                    $schema->addProperty($property, $valueProperty);
                }
            }
        }
        // RELATIONSHIP HAS PART OF
        foreach ($this->hasTypes as $propertyIsPartOf => $tableIsPartOf) {
            $className = "Plinct\\Api\\Type\\".ucfirst($tableIsPartOf);
            if (class_exists($className)) {
                $class = new $className();
                // ONE TO ONE
                if (self::ifExistsColumn($propertyIsPartOf)) {
                    if ($data[$propertyIsPartOf]) {
                        $dataIsPartOf = $class->get(['id'=>$data[$propertyIsPartOf]]);
                        $schema->addProperty($propertyIsPartOf, $dataIsPartOf[0]);
                    }
                }
                // ONE TO MANY
                else {
                    $dataIsPartOf = $class->get(['tableHasPart' => $this->tableHasPart, 'idHasPart' => $this->idHasPart]);
                    if (empty($dataIsPartOf) || is_null($dataIsPartOf[0])) $dataIsPartOf = null;
                    $schema->addProperty($propertyIsPartOf, $dataIsPartOf);
                }
            }
        }
        // IDENTIFIER
        if ($this->idHasPart) {
            $schema->addProperty('identifier', ["@type"=>"PropertyValue", "name" => "id", "value" => $this->idHasPart]);
        }
        return $schema->ready();
    }
}
