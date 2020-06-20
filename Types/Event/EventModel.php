<?php

namespace fwc\Thing;

/**
 * EventModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class EventModel extends Crud {
    protected $table = "event";

    public function __construct() {
        parent::__construct();
    }
    
    public function insert(array $data) {
        parent::insert($data);
        return parent::lastInsertId();
    }
    
    public function getByWords($words) {
        $query = "SELECT * FROM $this->table WHERE name LIKE '%$words%' ORDER BY startDate DESC;";
        return parent::getQuery($query);
    }
    
    // OBTER EVENTO POR IDEVENTS
    public function getEventByIdevents($idevent) {
        $query = "SELECT * FROM $this->table WHERE idevent=$idevent;";        
        return parent::getQuery($query);
    }
    
    public function getIdeventsByDateAndTitle($date, $name) {
        $namedecode = urldecode($name);
        $query = "SELECT idevent FROM $this->table WHERE name = '$namedecode' AND startDate LIKE '$date%';";
        $data = parent::getQuery($query);        
        return isset($data[0]) ? $data[0]['idevent'] : null;
    }
    
    public function getEventImages($idevent) {
        $query = "SELECT CONCAT(imageObject.location,'/',imageObject.name) AS src FROM imageObject, event_has_imageObject WHERE imageObject.idimageObject=event_has_imageObject.idimageObject AND event_has_imageObject.idevent=$idevent;";
        $data = parent::getQuery($query);        
        foreach ($data as $valueImage) {
                $imagesArray[] = $valueImage['src'];
        }
        return $imagesArray ?? null;
    }
    
    // OBTER EVENTOS PARA CONSTRUÇÃO DO SITEMAP
    public function getEventsForSitemaps() {
        $query = "SELECT * FROM event ORDER BY startDate desc";
        $dados = parent::getQuery($query);        
        foreach ($dados as $value) {
            $array['loc'] = "https://".$_SERVER['HTTP_HOST']."/eventos/".substr($value['startDate'],0,10)."/".urlencode($value['name']);            
            if($value['src']){
                $array['image'] = $value['src'];
            }            
            $array['news']['title'] = $value['name'];            
            $array['news']['date'] = $value['create_time'];            
            $return[] = $array;
        }        
        return $return;
    }
}
