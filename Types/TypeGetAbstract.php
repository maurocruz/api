<?php

namespace Fwc\Api\Type;

abstract class TypeGetAbstract extends ModelGet 
{    
    protected $schema = [];
    protected $types = []; 
    protected $properties = []; 
    protected $hasTypes = [];
    protected $hasProperties = [];

    public function __construct() 
    {
        parent::__construct();
        
        $this->setSchema("@context", "https://schema.org");
        $this->setSchema("@type", $this->type);         
    }

    protected function setSchema($name, $value)
    {
        $this->schema[$name] = $value;
    }

    public function setProperties($properties) 
    {
        $this->properties = array_unique(array_merge(func_get_args(), $this->properties));
    }
    
    public function setTypes($types) 
    {
        $this->types = func_get_args();
    }    
        
    private function getTypes($value) 
    {
        $idname = "id".lcfirst($this->table);
        $idOwner = $value[$idname];
        
        $types = array_unique(array_merge($this->hasTypes, $this->types));
        
        // address
        if (in_array('PostalAddress', $types) && $this->table != "postalAddress") {
            $value['address'] = json_decode((new PostalAddressGet())->selectById($value['address']), true);
            $this->setSchema('address', $value['address']);
        }
        // location
        if (in_array('Place', $types) && !in_array($this->table, [ "postalAddress", "place" ])) {
            $this->setSchema('location', json_decode((new PlaceGet())->selectById($value['location']), true));
        }
        // organization
        if (in_array('Organization', $types) && $this->table != "organization") {
            if (array_key_exists("publisher", $value)) {
                $this->setSchema('publisher', isset($value['publisher']) ? json_decode((new OrganizationGet())->selectById($value['publisher']), true) : null);
                
            } else {
                $this->setSchema('organization', isset($value['organization']) ? json_decode((new OrganizationGet())->selectById($value['organization']), true) : null);
            }
        }
                
        // image
        if (in_array('ImageObject', $types) && !in_array($this->table, [ "postalAddress", "person" ])) {
            $this->setSchema('image',json_decode((new ImageObjectGet())->getHasPart($this->table, $idOwner), true));
        }
        // person
        if (in_array('Person', $types) && !in_array($this->table, [ "person", "place", "postalAddress" ])) {
            if (array_key_exists("author", $value)) {
                $this->setSchema('author', json_decode((new PersonGet())->selectById($value['author']), true));
                
            } else {
                $this->setSchema('member', json_decode((new PersonGet())->getHasPart($this->table, $idOwner), true));
            }
        }        
        // contact point
        if (in_array("ContactPoint", $types) && !in_array($this->table, [ "place", "postalAddress" ])) {
            $this->setSchema('contactPoint', json_decode((new ContactPointGet())->getHasPart($this->table, $idOwner), true));
        }
    }
    
    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null) 
    {
        return $this->returnListAll( parent::index($where, $orderBy, $groupBy, $limit, $offset) );
    }
    
    /*public function listAll(string $where = null, $order = null, $limit = null, $offset = null) 
    {
        $data = parent::listAll($where, $order, $limit, $offset);
        return $this->returnListAll($data);
    }*/
    
    protected function returnListAll($data)
    {
        if (isset($data['error'])) {
           return json_encode($data); 
           
        } elseif (empty ($data)) {
            return json_encode(ItemList::list(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
        } else {
            foreach ($data as $key => $value) {                
                $list[] = $this->schema($value);
            }
            
            return json_encode(ItemList::list(count($data),$list), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);     
        }
    }
    
    protected function getHasPart($tableOwner, $idOwner, $order = null, $groupby = null) 
    {
        $data = parent::getHasPart($tableOwner, $idOwner, $order, $groupby);
        
        if (empty($data)) {
            return null;
            
        } else {
            foreach ($data as $value) {
                $list[] = $this->schema($value);
            }
            return json_encode($list, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }
    
    public function selectById($id, $order = null, $field = '*') 
    {
        $data = parent::selectById($id, $order, $field);
        
        if(empty($data)) {
            return null;
            
        } else {
            $value = $data[0];
            return json_encode($this->schema($value), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }  
    }
    
    protected function getSchema($value)
    {   
        $this->getTypes($value);
        
        $props = array_unique(array_merge($this->properties, $this->hasProperties));
        
        foreach ($props as $property)
        {
            $this->setSchema($property, $value[$property] ?? null);                        
        }
        
        return $this->schema;
    }
}
