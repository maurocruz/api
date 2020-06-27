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

    private function schema(array $value) 
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
                
                if (array_key_exists($valueProperty, $value)) {
                    $schema[$valueProperty] = $value[$valueProperty];
                }
                
                // relationships
                if (array_key_exists($valueProperty, $this->propertiesHasTypes)) {
                    $type = $this->propertiesHasTypes[$valueProperty];
                    
                     if (isset($schema[$valueProperty])) {

                    $urlDep = "http:".$host."/api/".lcfirst($type). "?id=".$value[$valueProperty];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $urlDep);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    $data = curl_exec($ch);
                    curl_close($ch);

                    $schema[$valueProperty] = json_decode($data, true);

                }
                // one to many
                else {                
                    $rel = new \Fwc\Api\Server\Relationships();
                    $data = $rel->getRelationship($this->table, $id, $type);

                    $schema[$valueProperty] = $data;
                }
                }
            }
        }
        
        // url
        if (isset($value['url']) && $value['url'] == null) {
            $schema['url'] = $url;
        }
        
        return $schema;
    }
}
