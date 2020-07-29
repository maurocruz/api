<?php

namespace Plinct\Api\Server;

trait SchemaTrait 
{
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
     * @param array $value
     * @return string
     */
    private function schema(array $value) 
    {
        $id = $value['id'.$this->table];
        
        $schema = [
            "@context" => "https://schema.org",
            "@type" => $this->type,
            "identifier" =>[
                [ "@type" => "PropertyValue", "name" => "id", "value" => $id ]
            ]
        ];        
               
        // add properties
        if (!empty($this->properties)) {
            
            foreach ($this->properties as $valueProperty) {
                $data = null;
                  
                // added properties on schema array
                if ($valueProperty == "*") {
                    foreach ($value as $key => $valueValue) {
                         $schema[$key] = $valueValue;
                    }                    
                } 
                
                if (array_key_exists($valueProperty, $value)) {                    
                    $schema[$valueProperty] = $value[$valueProperty];
                }
                
                // set relationships
                if (array_key_exists($valueProperty, $this->hasTypes)) {
                    
                    // set relational object type
                    $type = $this->hasTypes[$valueProperty];                    
                    $typeObjectName = "\\Plinct\\Api\\Type\\".$type;  
                    
                    if (class_exists($typeObjectName)) {
                        $typeObject = new $typeObjectName();
                        
                        // one to one
                        if (array_key_exists($valueProperty, $value)) {

                            if (is_numeric($id)) {
                                $resp = $typeObject->get([ "id" => $value[$valueProperty] ]);
                                $data = $resp[0] ?? null;
                            } else {
                                $data = null;
                            }
                        }
                        
                        // one to many
                        else {
                            $rel = (new \Plinct\Api\Server\Relationship())->getRelationship($this->table, $id, lcfirst($type));
                            
                            foreach ($rel as $valueRel) {
                               $data[] = $typeObject->schema($valueRel);                                
                            }
                        }                        
                    }
                    
                    $schema[$valueProperty] = $data;
                }
            }
        }
        
        return $schema;
    }
}
