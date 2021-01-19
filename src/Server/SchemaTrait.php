<?php

namespace Plinct\Api\Server;

trait SchemaTrait
{
    protected $tableHasPart;
        
    protected $idHasPart;
    
    protected $table;
    
    protected $type;
    
    protected $properties = [];
    
    protected $hasTypes = [];

    /**
     * GET SCHEMA WITH ARRAY ITEMS
     * @param array $data
     * @return array
     */
    protected function getSchema(array $data): array
    {
        if (empty($data)) {
            return $data;
        } 
        
        foreach ($data as $value) {
            $list[] = $this->schema($value);
        }
            
        return $list;
    }
    
    protected function listSchema($data, $numberOfList, $itemListOrder = "ascending"): array
    {
        
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
    public function schema(array $valueData): array
    {
        $this->idHasPart = $valueData['id'.$this->table];
                
        $schema = [
            "@context" => "https://schema.org",
            "@type" => $this->type
        ];        
               
        // add properties
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
                    $schema[$valueProperty] = parent::relationshipsInSchema($valueData, $valueProperty);
                }
            }
        }
        
        $schema['identifier'][] = [ "@type" => "PropertyValue", "name" => "id", "value" => $this->idHasPart ];
        
        return $schema;
    }
}
