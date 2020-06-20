<?php

/**
 * EventController
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class EventGet extends ThingGetAbstract implements ThingGetInterface
{
    protected $table = "event";
    protected $type = "Event";
    
    public function index(string $where = null, $order = null, $groupBy = null, $limit = null, $offset = null): string
    {
        parent::index($where, $order, $groupBy, $limit, $offset);
    }


    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) 
    {
        $order = "startDate DESC";
        $data = parent::listAll($where, $order, $limit, $offset);
        if(empty($data)) {
            return null;
        } else {
            foreach ($data as $value) {
                $value['location'] = json_decode((new PlaceGet())->selectById($value['location']), true);
                $value['image'] = json_decode((new ImageObjectGet())->getHasPart("event", $value['idevent']), true);
                $list[] = self::schema($value);
            }            
            return json_encode(ItemList::list(count($data), $list, "descendent", "Lista of event order by start date"), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Get data from idevent by CMS
     */
    public function selectById($id, $order = null, $field = '*') 
    {
        return parent::selectById($id, $order, $field);      
    }

    /**
     * Get data from site by date and title event
     */
    public function getEventByDateAndTitle($date, $name) 
    {
        $idevent = (new EventModel())->getIdeventsByDateAndTitle($date, $name);
        return parent::selectById($idevent);
    }
            
    public function getComingEvents()
    {             
        $field = "*, startDate, endDate";
        $where = "IF(`endDate`='00-00-0000 00:00:00', `startDate` >= CURDATE(), `endDate` >= CURDATE())";
        $groupby = "idevent";
        $order = "startDate ASC";          
        $data = parent::read($field, $where, $groupby, $order);
        
        return $this->returnListAll($data);        
    }    
    
    protected function schema($value) 
    {
        $this->setSchema("identifier", [
            [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idevent'] ]
        ]);
        $this->setSchema("name", $value['name']);
        $this->setSchema("description", $value['description']);
        $this->setSchema("startDate", $value['startDate']);
        $this->setSchema("endDate", $value['endDate']);
        $this->setSchema("url", "//".filter_input(INPUT_SERVER, "HTTP_HOST")."/data/event?fwc_id=".$value['idevent']);
        
        return $this->getSchema($value);
    }
}
