<?php

namespace fwc\Thing;

/**
 * TravelActionGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

class TravelActionGet extends ModelGet {
    protected $table = "travelAction";
    
    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) {
        $data = parent::listAll($where, $order, $limit, $offset);
        return parent::returnListAll($data, __CLASS__.'::schema');
    }


    public function selectById($id, $order = null, $field = '*') 
    {
        $data = parent::selectById($id, $order, $field);
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];          
            return json_encode(self::schema(self::setForeignValue($value)));
        }
    }
    
    public function selectByIdaction($idaction) 
    {
        $data = parent::read("*", "`idaction`=$idaction");
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];
            $value['fromLocation'] = json_decode((new PlaceGet())->selectById($value['fromLocation']), true);
            $value['toLocation'] = json_decode((new PlaceGet())->selectById($value['toLocation']), true);
            return json_encode(self::schema($value));            
        }
    }    

    static private function schema($value) 
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "TravelAction",
            "identifier" =>[
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idtravelAction']]
            ],
            "fromLocation" => $value['fromLocation'],
            "toLocation" => $value['toLocation'],
            "distance" => $value['distance'],
            "instrument" => $value['instrument']
        ];
    }
}
