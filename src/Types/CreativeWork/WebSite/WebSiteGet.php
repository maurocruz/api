<?php
namespace fwc\Thing;
/**
 * WebSiteGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class WebSiteGet extends ModelGet { 
    protected $table = "webSite";

    /*public function listAll($search = null, $order = null) {
        $data = (new WebSiteModel($this->settings))->listAll($search, $order);
        if (empty($data)) {
            return json_encode(ItemList::list(0));
        } else {
            if (array_key_exists('errorInfo', $data)) {
                return $data;
            } else {
                foreach ($data as $value) {
                    $list[] = self::webSite($value);
                }
                return json_encode(ItemList::list(count($list), $list));
            }            
        }
    }*/
    
    public function getById($id) {
        $data = parent::selectById($id);
        if (empty($data)) {
            return null;
        } else {
            $value = $data[0];
            return json_encode(self::webSite($value));
        }
    }
    
    static private function webSite($value) {
        return [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "identifier" => [
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idwebSite'] ]
            ],
            "name" => $value['name'],
            "description" => $value['description'],
            "url" => $value['url']
        ];
    }
    
}
