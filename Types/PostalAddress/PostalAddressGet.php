<?php

/**
 * PostalAddressGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class PostalAddressGet extends ThingGetAbstract implements ThingGetInterface 
{
    public $table = "postalAddress";
    public $type = "PostalAddress";

    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null): string
    {
        return $this->returnListAll( parent::index($where, $orderBy, $groupBy, $limit, $offset) );
    }
    
    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) 
    {
        return parent::listAll($where, $order, $limit, $offset);
    }
    
    public function selectById($id, $order = null, $field = '*') 
    {
        return parent::selectById($id, $order, $field);
    }
    
    public static function addressToString($value)
    {
        $address = $value['streetAddress'] ?? null;
        $address .=  $value['addressLocality'] ?  ", " . $value['addressLocality'] : null;
        $address .=  $value['addressRegion'] ?  ", " . $value['addressRegion'] : null;
        $address .=  $value['addressCountry'] ?  ", " . $value['addressCountry'] : null;
        $address .=  $address ? "." : null;        
        return $address;
    }

    protected function schema($value) 
    {
        $this->setSchema("identifier", [
            [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idpostalAddress'] ]
        ]);        
        $this->setSchema("streetAddress", $value['streetAddress']);
        $this->setSchema("addressLocality", $value['addressLocality']);
        $this->setSchema("addressRegion", $value['addressRegion']);
        $this->setSchema("addressCountry", $value['addressCountry']);
        $this->setSchema("postalCode", $value['postalCode']);
        $this->setSchema("url", "//".filter_input(INPUT_SERVER, "HTTP_HOST")."/data/PostalAddress?fwc_id=".$value['idpostalAddress']);
        
        return $this->getSchema($value);
    }
}
