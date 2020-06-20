<?php

/**
 * ActionGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class ActionGet extends ModelGet 
{
    protected $table = "action";
        
    public function getHasPart($tableOwner, $idOwner, $groupby = null, $order = null) {
        $data = parent::getHasPart($tableOwner, $idOwner, $groupby, $order);
        if(empty($data)) {
            return json_encode(ItemList::list());
        } else {
            foreach ($data as $value) {  
                $value['potentialAction'] = json_decode((\fwc\Cms\Helper\ClassFactory::createFwcThingClass($value['additionalType']))->selectByIdaction($value['idaction']), true);
                $list[] = self::schema(self::setForeignValue($value));
            }
            return json_encode(ItemList::list(count($data), $list));
        }
    }    

    private function setForeignValue($value) {              
        // agent
        $agentClassname = "\\fwc\\Thing\\".ucfirst($value['agentType'])."Get";
        $value['agent'] = class_exists($agentClassname) ? json_decode((new $agentClassname())->selectById($value['agentId']), true) : null;
        // from location
        $value['location'] = json_decode((new PlaceGet())->selectById($value['location']), true);
        return $value;
    }
    
    static private function schema($value) {
        return [
            "@context" => "https://schema.org",
            "@type" => "Action",
            "additionalType" => $value['additionalType'],
            "potentialAction" => $value['potentialAction'],
            "name" => $value['name'],
            "description" => $value['description'],
            "identifier" =>[
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idaction']]
            ],
            "startTime" => $value['startTime'],
            "startDate" => $value['startDate'],
            "endTime" => $value['endTime'],
            "endDate" => $value['endDate'],
            "agent" => $value['agent'] ?? null,
            "location" => $value['location']
        ];
    }
}
