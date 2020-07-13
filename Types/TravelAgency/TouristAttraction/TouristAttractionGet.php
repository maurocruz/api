<?php
namespace fwc\Thing;
/**
 * TouristAttractionGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class TouristAttractionGet extends ThingGetAbstract
{
    protected $table = "touristAttraction";

    public function listAll($where = null, $order = null, $limit = null, $offset = null) 
    {
        $data = parent::listAll($where, $order, $limit, $offset);
        
        if(array_key_exists("errorInfo",$data)) {
            return json_encode($data);
            
        } elseif (empty ($data)) {            
            return ItemList::list();
            
        } else {
            foreach ($data as $value) {
                $value['location'] = json_decode((new PlaceGet())->selectById($value['location']), true);
                $list[] = self::schema($value);
            }
            
            return json_encode(ItemList::list(count($list), $list));
        }        
    }
    
    public function selectById($id, $order = null, $field = '*') 
    {
        $data = parent::selectById($id, $order, $field);
        if(empty($data)) {
            return null;
            
        } else {
            $value = $data[0];
            $value['location'] = json_decode((new PlaceGet())->selectById($value['location']), true);
            return json_encode(self::schema($value));
        }
    }
    
    protected static function schema($value) 
    {
        return [
            "@context" => "https://schema.org",
            "@type" => "TouristAttraction",
            "additionalType" => $value['location']['additionalType'],
            "identifier" => [
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idtouristAttraction'] ]
            ],
            "name" => $value['location']['name'],
            "touristType" => $value['touristType'],
            "availableLanguage" => $value['availableLanguage'],
            "location" => $value['location'],
            "touristDestination" => $value['touristDestination'],
            "localBusiness" => $value['localBusiness']
        ];
    }
}
