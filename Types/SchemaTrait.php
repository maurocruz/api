<?php

namespace Fwc\Api\Type;

trait SchemaTrait 
{
    protected $table;
    
    protected $type;
    
    protected $properties = [];
    
    protected $propertiesHasTypes = [];

    protected function listSchema($data) 
    {
        if (empty($data)) {
            return [ "messagem" => "Not founded" ];
        } 
        
        foreach ($data as $value) {            
            $list[] = $this->schema($value);
        }
            
        return $list;
    }

    public function schema(array $value) 
    {
        $id = $value['id'.$this->table];
        $host = "//".$_SERVER['HTTP_HOST'];
        $urlApi = $host.$_SERVER['REQUEST_URI'];
        $url = $urlApi."?id=".$id;
        
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
                $data = null;
                
                // added properties on schema array
                if (array_key_exists($valueProperty, $value)) {
                    $schema[$valueProperty] = $value[$valueProperty];
                }
                
                // set relationships
                if (array_key_exists($valueProperty, $this->propertiesHasTypes)) {
                    
                    // set relational object type
                    $type = $this->propertiesHasTypes[$valueProperty];                    
                    $typObjectName = __NAMESPACE__.'\\'.$type;  
                    
                    if (class_exists($typObjectName)) {
                        $typeObject = new $typObjectName($this->request);
                        
                        // one to one
                        if (array_key_exists($valueProperty, $value)) {
                            $id = $value[$valueProperty];

                            if (is_numeric($id)) {
                                $resp = $typeObject->get([ "id" => $id ]);
                                $data = $resp[0];
                            } else {
                                $data = null;
                            }

                        }
                        // one to many
                        else {
                            $rel = (new \Fwc\Api\Server\Relationships())->getRelationship($this->table, $id, lcfirst($type));

                            foreach ($rel as $valueRel) {
                                $data[] = $typeObject->schema($valueRel);                                
                            }
                        }
                    }
                    
                    $schema[$valueProperty] = $data;
                }
            }
        }
        
        // url
        if (array_key_exists('url', $schema) && $schema['url'] == null) {
            $schema['url'] = $url;
        }
        
        return $schema;
    }
}
