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
            } elseif (array_search($property,$this->properties) !== false) {
                $schema->addProperty($property, $valueProperty);
            } else {
                $schema->addProperty($property, $valueProperty);
            }
        }
        // RELATIONSHIP IS PART OF
        foreach ($this->hasTypes as $propertyIsPartOf => $tableIsPartOf) {
            // IF TYPE IS PART OF IS DEFINED WITH FIELD TYPE BD
            if ($tableIsPartOf === true) {
                $tableIsPartOf = $data[$propertyIsPartOf.'Type'];
            }
            // GET OBJECT TYPT PART OF
            $className = "Plinct\\Api\\Type\\".ucfirst($tableIsPartOf);
            if (class_exists($className)) {
                $class = new $className();
                // RELATIONSHIP ONE TO ONE
                if (self::ifExistsColumn($propertyIsPartOf)) {
                    if (isset($data[$propertyIsPartOf])) {
                        $dataIsPartOf = $class->get(['id'=>$data[$propertyIsPartOf]]);
                        $schema->addProperty($propertyIsPartOf, $dataIsPartOf[0]);
                    }
                }
                // RELATIONSHIP ONE TO MANY
                else {
                    if ($tableIsPartOf == "Offer") {
                        $params = [ "itemOfferedType" => $this->tableHasPart, "itemOffered" => $this->idHasPart ];
                    } elseif ($tableIsPartOf == "Invoice" || $tableIsPartOf == 'OrderItem') {
                        $params = ['referencesOrder'=>$this->idHasPart];
                    } elseif (isset($class->getHasType()['isPartOf']) && $class->getHasType()['isPartOf']==ucfirst($this->tableHasPart)) {
                        $params = ['isPartOf'=>$this->idHasPart];
                    } else {
                        $params = ['tableHasPart'=>$this->tableHasPart,'idHasPart'=>$this->idHasPart];
                    }
                    $dataIsPartOf = $class->get($params);
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
