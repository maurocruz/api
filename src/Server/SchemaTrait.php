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
     * @param type $data
     * @return type
     */
    protected function getSchema($data) 
    {
        if (empty($data)) {
            return [ "message" => "No data founded" ];
        } 
        
        foreach ($data as $value) {
            $list[] = $this->schema($value);
        }
            
        return $list;
    }
    
    protected function listSchema($data, $numberOfList, $itemListOrder = "ascending")
    {        
        if (empty($data)) {
            return [ "messagem" => "No data founded" ];
        } 
        
        $itemList = [
            "@context" => "http://schema.org",
            "@type" => "ItemList",
            "numberOfItems" => $numberOfList,
            "itemListOrder" => $itemListOrder
        ];
        
        foreach ($data as $key => $value) {
            $listItem[] = [
                "@type" => "ListItem",
                "position" => ($key+1),
                "item" => $this->schema($value)
            ];
        }
            
        $itemList["itemListElement"] = $listItem;
                
        return $itemList;
    }

    /**
     * SCHEMA
     * @param array $valueData
     * @return string
     */
    public function schema(array $valueData) 
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
