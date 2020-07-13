<?php
namespace fwc\Thing;
/**
 * WebPageElementGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class WebPageElementGet extends ModelGet {
    public $table = "webPageElement";
    
    public function getByPageId($identifier) {
        $data = parent::selectByNameValue("idwebPage", $identifier, "*", "position ASC");
        if (empty($data)) {
            return ItemList::list();
        } else {
            foreach ($data as $key => $value) {
                // cssSelector
                $value['cssSelector'] = json_decode((new PropertyValueGet())->getPropertyValueWithStrings('webPageElement', $value['idwebPageElement']), true);            
                // images
                $value['image'] = json_decode((new ImageObjectGet())->getHasPart('webPageElement', $value['idwebPageElement'], "position ASC"), true);            
                $list[] = self::getWebPageElement($identifier, $value);            
            }        
            return json_encode(ItemList::list(count($data), $list));
        }
    }
    
    private static function getWebPageElement($identifier, $value) {
        return [
            "@context" => "https://schema.org",
            "@type" => "WebPageElement",
            "identifier" => [
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idwebPageElement'] ]
            ],
            "name" => htmlentities($value['name'], ENT_QUOTES),
            "text" => htmlentities($value['text'], ENT_QUOTES),
            "position" => $value['position'],
            "cssSelector" => $value['cssSelector'] ?? null,
            "image" => $value['image'],
            "isPartOf" => [
                "@type" => "WebPage",
                "identifier" => $identifier
            ],
            "author" => $value['author'],
            "dateModified" => $value['dateModified'] ?? null,
            "dateCreated" => $value['dateCreated'] ?? null
        ];
    }    
}
