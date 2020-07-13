<?php
namespace fwc\Thing;

/**
 * BreadcrumbList
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class BreadcrumbList 
{
    public function getBreadcrumb($url) {                
        $itens = array_filter(explode("/", $url));                
        $oldUrl = null;
        $i = 1;
        foreach ($itens as $key => $value) {            
            $url = $oldUrl ? $oldUrl.'/'.$value : "/".$value;
            $oldUrl = $url;            
            $data = (new WebPageModel())->getByUrl($url);            
            $name = $data[0]['alternativeHeadline'] ?? ucfirst($value);  
            $itemListElement[] = [
                "@type" => "ListItem",
                "position" => $i,
                "item" => [
                    "@id" => $url,
                    "name" => htmlentities($name)
                ]
            ];
            $i++;
        }        
        $array = [
            "@context" => "https://schema.org",
            "@type" => "BreadcrumbList",
            "numberOfItems" => count($itens),
            "itemListElement" => $itemListElement ?? null
        ];        
        return json_encode($array);        
    } 
}
