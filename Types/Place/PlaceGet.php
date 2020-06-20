<?php

/**
 * PlaceController
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

declare(strict_types=1);

namespace fwc\Thing;

class PlaceGet extends ThingGetAbstract implements ThingGetInterface
{
    protected $table = "place";
    protected $type = "Place";
    protected $hasTypes = [ "PostalAddress" ];

    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null): string
    {
        return $this->returnListAll( parent::index($where, $orderBy, $groupBy, $limit, $offset) );
    }
    
    public function listAll($where = null, $order = null, $limit = null, $offset = null) 
    {
        return parent::listAll($where, $order, $limit, $offset);
    }

    public function selectById($id, $order = null, $field = '*')
    {
        return parent::selectById($id, $order, $field);
    }
    
    public function selectNameByAjax($queryParams) : string 
    {
        $data = parent::selectByName($queryParams['name']);
        
        if (empty($data)) {
            $list = null;
            
        } else {
            foreach ($data as $value) {
                $list[] = [ "@type" => "Place", "name" => $value['name'], "identifier" => $value['idplace'] ];
            }
        }
        
        $array = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "itemListElement" => $list
        ];
        
        return json_encode($array);
    }
    
    protected function schema($value = null)
    {        
        // identifier
        $this->setSchema("identifier", [
            [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idplace'] ],
            [ "@type" => "PropertyValue", "name" => "dateRegistration", "value" => $value['create_time'] ]
        ]);
        // additionalType
        $this->setSchema("additionalType", $value['additionalType']);
        // name
        $this->setSchema("name", $value['name']);
        // description
        $this->setSchema("description", $value['description']);
        // disambiguatingDescription
        $this->setSchema("disambiguatingDescription", $value['disambiguatingDescription']);
        // geo
        $this->setSchema("geo", [
            "@type" => "GeoCoordinates",
            "latitude" => $value['latitude'],
            "longitude" => $value['longitude'],
            "elevation" => $value['elevation']
        ]);
        
        return $this->getSchema($value);
    }
}
