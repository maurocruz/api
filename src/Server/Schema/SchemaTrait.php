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


    /**
     * GET SCHEMA WITH ARRAY ITEMS
     * @param array $data
     * @return array
     */
    /*protected function getSchema(array $data): array {
        $list = [];
        if (empty($data)) {
            return [];
        }
        foreach ($data as $value) {
            $list[] = $this->schema($value);
        }
        return $list;
    }*/

    /**
     * SCHEMA
     * @param array $valueData
     * @return array
     */
   /* public function schema(array $valueData): array {
        $this->idHasPart = $valueData['id'.$this->table];
        $this->tableHasPart = $this->table;
        $fields = $this->getParams()['fields'] ?? null;
        $type = $this->type;
        $schema = new Schema($type);
        // FIELDS
        if ($fields) {
            $fieldsExplode = explode(",", $fields);
            foreach ($fieldsExplode as $fieldsValue) {
                $schema->addProperty($fieldsValue, $valueData[$fieldsValue]);
            }
        }
        // PROPERTIES
        if (!empty($this->properties)) {
            foreach ($this->properties as $valueProperty) {
                // added properties on schema array if $properties is defined with *
                if ($valueProperty == "*") {
                    foreach ($valueData as $key => $valueValue) {
                        $schema->addProperty($key, $valueValue);
                    }                    
                } 
                // add properties defined others type $properties
                if (array_key_exists($valueProperty, $valueData)) {
                    $schema->addProperty($valueProperty, $valueData[$valueProperty]);
                }
                // set relationships
                if (array_key_exists($valueProperty, $this->hasTypes)) {
                    $schema->addProperty($valueProperty, self::relationshipsInSchema($valueData, $valueProperty));
                }
            }
        }
        $schema->setIdentifier([ "@type" => "PropertyValue", "name" => "id", "value" => $this->idHasPart ]);
        return $schema->ready();
    }*/

    /*private function setProperties(string $propertiesParams) {
        $array = null;
        $propArray = explode(",", $propertiesParams);
        foreach ($propArray as $value) {
            $array[] = trim($value);
        }
        $this->properties = $array ? array_merge($this->properties, $array) : $this->properties;
    }*/
}
