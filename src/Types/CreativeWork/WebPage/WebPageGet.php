<?php
namespace fwc\Thing;
/**
 * WepPageGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class WebPageGet extends ModelGet {
    protected $table = "webPage";
    
    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) {
        $data = parent::listAll($where, $order, $limit, $offset);
        return parent::returnListAll($data, __CLASS__.'::schemaComplete');
    }

    public function getData($url = null) {
        if (is_null($url)) {
            $explode = explode("/", filter_input(INPUT_SERVER, "REQUEST_URI"));
            $url = "/". implode("/", array_filter($explode));
        }
        return (new WebPageModel())->getJsonByUrl($url) ?? $this->getCompletePageByUrl($url);
    }
    
    public function getDataWithJson($url) { // deprecated
        return (new WebPageModel())->getJsonByUrl($url);
    }

    public function getCompletePageByUrl($url) {
        $data = (new WebPageModel())->getByUrl($url);
        return empty($data) ? null : $this->returnCompleteSchema($data[0]['idwebPage'], $data[0]);
    }

    public function selectById($id, $order = null, $field = '*') {
        $data  = parent::selectById($id, $order, $field);        
        if(empty($data)) {
            return null;
        } else {
            $value = $data[0];
            return self::returnCompleteSchema($id, $value);
        }
    }
    
    private function returnCompleteSchema($identifier, $value) {
        // page attributes
        $value['cssSelector'] = json_decode((new PropertyValueGet())->getPropertyValueWithStrings('webPage', $identifier), true);        
        // breadcrumb
        $value['breadcrumb'] = json_decode((new BreadcrumbList())->getBreadcrumb($value['url']));        
        // page elements
        $value['hasPart'] = json_decode((new WebPageElementGet())->getByPageId($identifier), true);        
        // primaryImageOfPage
        $value['primaryImageOfPage'] = $value['hasPart']['numberOfItems'] > 0 ? self::getPrimaryImageOfPage($value['hasPart']['itemListElement']) : null;        
        return json_encode(self::schemaComplete($value));
    }
        
    static private function getPrimaryImageOfPage($hasPart) {  
        foreach ($hasPart as $value) {
            $item = $value['item'];
            if($item['image']) {
                foreach ($item['image'] as $key => $valueImage) {
                    $images[] = $valueImage['url'];
                    if ($valueImage['representativeOfPage'] !== '0' and $valueImage['representativeOfPage'] !== null) {
                        $primaryImageOfPage = $valueImage['url'];
                        break;
                    }                
                }
            }
        }        
        return $primaryImageOfPage ?? (isset($images) ? reset($images) : null);        
    }

    public function getForMapSite() {
        $data = (new WebPageModel())->getAllPages();                        
        foreach ($data as $value) {            
            $list[] = self::schemaComplete($value);
        }        
        $return = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "itemListOrder" => "ItemListOrderAscending",
            "numberOfItens" => count($data),
            "itemListElement" => $list            
        ];        
        return json_encode($return);
    }
    
    protected static function schemaComplete($value) {           
        return [
            "@context" => "https://schema.org",
            "@type" => "WebPage",
            "identifier" => [
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idwebPage'] ]
            ],
            "name" => htmlentities($value['name']),
            "description" => htmlentities($value['description']),
            "url" => "//" . $_SERVER['HTTP_HOST'] . (substr($value['url'], 0, 1) == "/" ? $value['url'] : $value['url']),
            "primaryImageOfPage" => $value['primaryImageOfPage'] ?? null,
            "cssSelector" => $value['cssSelector'] ?? null,
            "alternativeHeadline" => $value['alternativeHeadline'],
            "lastReviewed" => $value['dateModified'],
            "dateCreated" => $value['dateCreated'],
            "breadcrumb" => $value['breadcrumb'],
            "potentialAction" => [
                [
                    "@type" => "Action",
                    "name" => "showTitle",
                    "result" => $value['showtitle']
                    
                ],
                [
                    "@type" => "Action",
                    "name" => "showDescription",
                    "result" => $value['showdescription']
                    
                ]
            ],
            "hasPart" => $value['hasPart'] ?? null
        ];
    }
}
