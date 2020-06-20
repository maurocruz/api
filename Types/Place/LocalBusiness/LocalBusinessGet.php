<?php

/**
 * ProductGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

declare(strict_types=1);

namespace fwc\Thing;

class LocalBusinessGet extends ThingGetAbstract implements ThingGetInterface
{
    public $table = "localBusiness";
    protected $type = "LocalBusiness";
    protected $hasTypes = [ "Place" ];

    public function index($where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null): string
    {
        return parent::index($where, $orderBy, $groupBy, $limit, $offset);
    }

    public function listAll($where = null, $order = "name ASC", $limit = null, $offset = null, $itemListOrder = null) 
    {
        return parent::listAll($where, $order, $limit, $offset);
    }

    public function selectById($id, $order = null, $field = '*') 
    {
        return parent::selectById($id, $order, $field);
    }
    
    public function selectByNameAndId($name, $id) 
    {
        $data = parent::read("*", "REPLACE(' ', '', `name`) = '$name' OR `idlocalBusiness`='$id'");
        
        if (empty($data)) {
            return null;
            
        } else {
            $value = $data[0];
            $value['image'] = json_decode((new ImageObjectGet())->getHasPart("localBusiness", $value['idlocalBusiness']), true);            
            // place
            $value['location'] = json_decode((new PlaceGet())->selectById($value['location']), true);
            // address
            $value['address'] = json_decode((new PostalAddressGet())->selectById($value['address']), true);
            // contact point
            $value['contactPoint'] = json_decode((new ContactPointGet())->getCompleteContactPointPartOf("localBusiness", $value['idlocalBusiness']), true);
            return json_encode( $this->schema($value), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }
    
    protected function schema($value): array 
    {             
        $this->setSchema("identifier", [
            [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idlocalBusiness']]
        ]);
        $this->setSchema("url", $value['url']);
        
        $this->getSchema($value);
                
        $this->setSchema("name", $this->schema['location']['name']);
        
        return $this->schema;
    }
}
