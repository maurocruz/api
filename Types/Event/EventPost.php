<?php
namespace fwc\Thing;
/**
 * EventPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class EventPost extends ModelPost {
    public $table = "event";


    public function add(): string {
        //add
        $data = self::setData($this->POST);
        $idevent = parent::createNewAndReturnLastId($data);
        //sitemap
        $this->updateSitemap();    
        return "/admin/event/edit/$idevent";
    }   
    
    public function edit(): bool {  
        $idevent = $this->POST['idevent'];
        unset($this->POST['idevent']);
        $post = self::setData($this->POST);
        // update
        parent::update($post, "idevent=$idevent");        
        //sitemap
        $this->updateSitemap();        
        return true;        
    }
    
    public function erase() {
        $idevent = $this->POST['idevent'];        
        //delet
        parent::delete([ "idevent" => $idevent ]);        
        //sitemap
        $this->updateSitemap();        
        return "/admin/event";
    } 
    
    private static function setData($post) {
        // start time
        $startTime = $post['startTime'] == "" ? "00:00:00" : $post['startTime'];
        // end date and time
        $endDate = $post['endDate'] == '' ? $post['startDate'] : $post['endDate'];
        $endTime = $post['endTime'] == '' ? "00:00:00" : $post['endTime'];
        return [
            "name" => addslashes($post['name']),
            "startDate" => $post['startDate']."T".$startTime,
            "endDate" => $endDate."T".$endTime,
            "description" => addslashes($post['description']),
            "organizerType" => $post['organizerType'],
            "organizerId" => $post['organizerId']
        ];
    }   
    
    private function updateSitemap() {   
        (new \fwc\Cms\Helpers\SitemapHelper())->saveSitemaps((new EventModel($this->settings))->getEventsForSitemaps(), 'news', 'event');
    }
}
