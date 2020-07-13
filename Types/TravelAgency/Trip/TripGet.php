<?php

/**
 * TripGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class TripGet extends ThingGetAbstract implements ThingGetInterface 
{
    protected $table = "trip";
    protected $type = "Trip";

    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null): string {
        parent::index($where, $orderBy, $groupBy, $limit, $offset);
    }
    
    public function getOfProvider($providerId) 
    {
        $data = parent::read("*", "providerId=$providerId");
        
        if(isset($data['errorInfo'])) {
            return json_encode($data);
            
        } else {
            if (empty($data)) {
                return json_encode(ItemList::list());
                
            } else {
                foreach ($data as $value) {
                    // provider
                    $value['provider'] = json_decode((new TravelAgencyGet())->selectById($value['providerId']), true);
                    // itinerary
                    $value['itinerary'] = json_decode((new ActionGet())->getHasPart("trip", $value['idtrip'], "startTime"), true);
                    // image
                    $value['image'] = json_decode((new ImageObjectGet())->getHasPart('trip', $value['idtrip']), true);
                    $items[] = self::completeSchema($value);
                }
                return json_encode(ItemList::list(count($items), $items));
            }
        }
    }
    
    public function primaryOfferOfProvider($providerId) 
    {        
        return parent::listAll("providerId=$providerId");
    }
    
    public function selectById($id, $order = null, $field = '*') {
        $data = parent::selectById($id, $order, $field);
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];
            // provider
            $value['provider'] = json_decode((new TravelAgencyGet())->selectById($value['providerId']), true);
            // itinerary
            $value['itinerary'] = json_decode((new ActionGet())->getHasPart("trip", $id, "startTime"), true);
            // offers
            $value['offers'] = json_decode((new OfferGet())->getHasPart("trip", $id), true);
            // images
            $value['image'] = json_decode((new ImageObjectGet())->getHasPart("trip", $id), true);
            return json_encode(self::completeSchema($value));
        }
    }
    
    /**
     * return one trip json-ld for public exposure
     * @param string url
     * @return strin json
     */
    public function selectByUrl($url) {
        $data = parent::selectByUrl($url);
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];
            $value['image'] = json_decode((new ImageObjectGet())->getHasPart("trip", $value['idtrip']), true);
            $value['offers'] = json_decode((new OfferGet())->getHasPartForPublic("trip", $value['idtrip']), true);
            $value['itinerary'] = json_decode((new ActionGet())->getHasPart("trip", $value['idtrip'], null, "startDate, startTime"), true);
            return json_encode(self::completeSchema($value));
        }
    }

    private function completeSchema($value) 
    {
        $trip = self::schema($value);
        $trip['identifier'] = [ [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idtrip'] ] ];
        $trip['itinerary'] = $value['itinerary'];        
        return $trip;
    }
    
    protected function schema($value) 
    {
        $this->setSchema("name", $value['name']);
        $this->setSchema("url", $value['url']);
        $this->setSchema("description", $value['description']);
        
        
        return $this->getSchema($value);
        
        /*return [
            "@context" => "https://schema.org",
            "@type" => "Trip",
            "additionalType" => $value['additionalType'],
            "name" => $value['name'],
            "description" => $value['description'],
            "disambiguatingDescription" => $value['disambiguatingDescription'],
            "url" => $value['url'],
            "offers" => $value['offers'] ?? null,
            "provider" => $value['provider'] ?? null,
            "image" => $value['image']
        ];*/
    }
}
