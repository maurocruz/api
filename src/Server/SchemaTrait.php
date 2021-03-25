<?php
namespace Plinct\Api\Server;

use Plinct\PDO\PDOConnect;

trait SchemaTrait {
    protected string $tableHasPart;
    protected ?int $idHasPart;
    protected string $table;
    protected string $type;
    protected array $properties = [];
    protected array $hasTypes = [];

    protected function buildSchema($params, $data): array {
        if(isset($params['properties'])) {
            $this->setProperties($params['properties']);
        }
        if (array_key_exists('error', $data)) {
            return $data;
        } else {
            // format ItemList
            if (isset($params['format']) && $params['format'] == "ItemList") {
                if (isset($params['count']) && $params['count'] == "all") {
                    $countAll = PDOConnect::run("SELECT COUNT(*) as q FROM `$this->table`;");
                    $numberOfItems = $countAll[0]['q'];
                } else {
                    $numberOfItems =  count($data);
                }
                return $this->listSchema($data, $numberOfItems);
            }
            return $this->getSchema($data);
        }
    }
    /**
     * GET SCHEMA WITH ARRAY ITEMS
     * @param array $data
     * @return array
     */
    protected function getSchema(array $data): array {
        $list = [];
        if (empty($data)) {
            return [];
        }
        foreach ($data as $value) {
            $list[] = $this->schema($value);
        }
        return $list;
    }
    
    protected function listSchema($data, $numberOfList, $itemListOrder = "ascending"): array {
        $listItem = [];
        $itemList = [
            "@context" => "http://schema.org",
            "@type" => "ItemList",
            "numberOfItems" => $numberOfList,
            "itemListOrder" => $itemListOrder
        ];
        if (empty($data)) {
            $listItem = [];
        } else {
            foreach ($data as $key => $value) {
                $listItem[] = [
                    "@type" => "ListItem",
                    "position" => ($key + 1),
                    "item" => $this->schema($value)
                ];
            }
        }
        $itemList["itemListElement"] = $listItem;
        return $itemList;
    }
    /**
     * SCHEMA
     * @param array $valueData
     * @return array
     */
    public function schema(array $valueData): array {
        $this->idHasPart = $valueData['id'.$this->table];
        $this->tableHasPart = $this->table;
        $schema = [ "@context" => "https://schema.org", "@type" => $this->type ];
        // PROPERTIES
        if (!empty($this->properties)) {
            foreach ($this->properties as $valueProperty) {
                $data = null;
                // added properties on schema array if $properties is defined with *
                if ($valueProperty == "*") {
                    foreach ($valueData as $key => $valueValue) {
                         $schema[$key] = $valueValue;
                    }                    
                } 
                // add properties defined others type $properties
                if (array_key_exists($valueProperty, $valueData)) {
                    $schema[$valueProperty] = $valueData[$valueProperty];
                }
                // set relationships
                if (array_key_exists($valueProperty, $this->hasTypes)) {
                    $schema[$valueProperty] = self::relationshipsInSchema($valueData, $valueProperty);
                }
            }
        }
        $schema['identifier'][] = [ "@type" => "PropertyValue", "name" => "id", "value" => $this->idHasPart ];
        return $schema;
    }

    private function setProperties(string $propertiesParams) {
        $array = null;
        $propArray = explode(",", $propertiesParams);
        foreach ($propArray as $value) {
            $array[] = trim($value);
        }
        $this->properties = $array ? array_merge($this->properties, $array) : $this->properties;
    }
}
