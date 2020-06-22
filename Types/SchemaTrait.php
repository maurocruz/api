<?php

namespace Fwc\Api\Type;

trait SchemaTrait 
{
    protected $properties = [];
    protected $table;
    protected $type;
    
    protected function listItem(array $list = null, $ordering = null): array 
    {
        $itemListOrder = stripos($ordering,'asc') !== false ? "ascending" : 
                ( stripos($ordering,"desc") !== false ? "descending" : 
                (stripos($ordering, 'rand') !== false ? "randomly" : "unordering") );        
        
        if (!$list) {
            $name = "Empty list";
            $numberOfItems = 0;
        } else {
            $name = "list of ".$this->type;
            $numberOfItems = count($list);
            foreach ($list as $i => $valueList) {
                $itemListElement[] = [
                    "@type" => "ListItem",
                    "position" => $i+1,
                    "item" => $valueList
                ];
            }
        }
                
        return [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "name" => $name,
            "numberOfItems" => $numberOfItems,
            "itemListOrder" => $itemListOrder,
            "itemListElement" => $itemListElement ?? null
        ]; 
    }
    
    protected function propertiesMerge(string $propertiesIncrement) 
    {
        $propArray = explode(",", $propertiesIncrement);
        $this->properties = array_merge($this->properties, $propArray);
    }
    
    private function schema(array $value) 
    {
        $id = $value['id'.$this->table];
        $urlApi = "//".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?id=".$id;
        
        $schema = [
            "@context" => "https://schema.org",
            "@type" => $this->type,
            "identifier" =>[
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $id ]
            ]
        ];        
                
        // add properties
        if (!empty($this->properties)) {
            foreach ($this->properties as $valueProperty) {
                if (array_key_exists($valueProperty, $value)) {
                    $schema[$valueProperty] = $value[$valueProperty];
                }
            }
        }
        
        // url
        if (isset($value['url']) && $value['url'] == null) {
            $schema['url'] = $urlApi;
        }
        
        return $schema;
    }
}
