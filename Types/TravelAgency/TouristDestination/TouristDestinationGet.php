<?php

/**
 * TouristDestinationGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class TouristDestinationGet extends ModelGet 
{
    protected $table = "touristDestination";

    public function listAll($where = null, $order = null, $limit = null, $offset = null) 
    {
        $data = parent::listAll($where, $order, $limit, $offset);
        
        if (isset($data['errorInfo'])) {
           return json_encode($data);
        } elseif (empty ($data)) {
            return ItemList::list();        
        } else {
            foreach ($data as $value) {
                $place = json_decode((new PlaceGet())->selectById($value['location']), true);
                $place['@type'] = "TouristDestination";
                $place['identifier'][0]['value'] = $value['idtouristDestination'];
                $place['touristType'] = $value['touristType'];
                $list[] = $place;
            }
            return json_encode(ItemList::list(count($list), $list));
        }
    }
    
    public function selectById($id, $order = null, $field = '*') 
    {
        $data = parent::selectById($id, $order, $field);
        if (empty($data)) {
            return false;
        } else {
            $value = $data[0];
            $value['place'] = json_decode((new PlaceGet())->selectById($value['location']), true);
            return json_encode(self::touristDestination($value));
        }
    }
    
    private function touristDestination($value) 
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "TouristDestination",
            "name" => $value['place']['name'],
            "identifier" => [
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idtouristDestination'] ]
            ],
            "touristType" => $value['touristType'],
            "place" => $value['place']
        ];              
    }
    
}
