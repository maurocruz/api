<?php
namespace fwc\Thing;

/**
 * BannerGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class BannerGet 
{    
    public function getResponsivesBanners($target = null, $position = null) 
    {
        $data = (new BannerModel())->getResponsivesBanner($target, $position);
        
        foreach ($data as $value) {
            $images = json_decode((new \fwc\Thing\ImageObjectGet())->getHasPart("banners", $value['idbanners']));
            $list[] = self::getItem($value, $images);
        }
        
        return json_encode([
            "@type" => "ItemList",
            "name" => "List of banners",
            "numberOfItems" => count($data),
            "itemListElement" => $list ?? null
        ]);
    }
    
    public function getBannerByIdcontract($idcontract) {
        $data = (new BannerModel())->getItensByIdContract($idcontract);
        if (!empty($data)) {
            $value = $data[0] ?? null;
            $images = json_decode((new \fwc\Thing\ImageObjectGet())->getHasPart("banners", $value['idbanners']));
            $content = self::getItem($value, $images);
        } else {
            $content = null;
        }
        return json_encode($content) ?? null;
    }
    
    static private function getItem($value, $images = null) {
        return [
            "@type" => "CreativeWork",
            "name" => $value['banner_title'],
            "url" => $value['banner_link'],
            "isPartOf" => [ 
                "@type" => "Banner",
                "identifier" => $value['idadvertising']
            ],
            "identifier" => [ 
                [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idbanners'] ]
            ],
            "description" => $value['banner_text'],
            "target" => $value['target'],
            "position" => $value['position'],
            "status" => $value['status'],
            "tags" => $value['tags'],
            "style" => $value['style'],
            "image" => $images
        ];
    }
}
