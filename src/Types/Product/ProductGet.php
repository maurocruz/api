<?php

namespace fwc\Thing;

/**
 * ProductGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ProductGet 
{
    public  $settings;
    
    public function __construct($settings) {
        $this->settings = $settings;
    }
        
    public function getAll($order = null)
    {
        $data = (new ProductModel($this->settings))->getAll($order);
        
        foreach ($data as $value) {
            $list[] = self::getItem($value);
        }
        
        $content = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "numberOfItems" => count($data),
            "itemListElement" => $list
        ];
        
        return json_encode($content);
    }
    
    public function getProduct($idProduct, $order = null)
    {
        // product
        $data = (new ProductModel($this->settings))->getProductById($idProduct, $order);        
        $item = self::getItem($data[0]);
        
        // images
        $data = (new ImageObjectGet($this->settings))->getListImagesidPartOf("product", $idProduct, "position ASC");
        $item['image'] = json_decode($data, true);
        
        return json_encode($item);
    }
    
    public function getProdutosInStockByAdditionalType($additionalType, $order = null)
    {
        $data = (new ProductModel($this->settings))->getProductInStockByAdditionalType($additionalType, $order);
        
        if (empty($data)) {
            $list = null;
        } else {
            foreach ($data as $value) {
                $item = self::getItem($value, $additionalType);
                // images
                $dataImages = (new ImageObjectGet($this->settings))->getImagesHasTable("product", $value['idproduct'], "position ASC LIMIT 1");
                $item['image'] = json_decode($dataImages, true);  
                
                $list[] = $item;
            }
        }
        
        
        $return = [
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "name" => "List of Products with additional type: ".$additionalType,
            "numberOfItems" => count($data),
            "itemListElement" => $list            
        ];
        
        return json_encode($return);
    }

    private static function getItem($value, $additionalType = null)
    {
        $type = $additionalType ? [ "Product", $additionalType ] : "Product";
        return [
            "@type" => $type,
            "additionalType" => $value['additionalType'],
            "name" => $value['name'],
            "identifier" => $value['idproduct'],
            "description" => $value['description'],
            "category" => $value['category'],
            "position" => $value['position'],
            "offers" => [
                "@type" => "Offer",
                "availability" => $value['availability']
            ]
        ];
    }    
}
