<?php

/**
 * ImageObjectGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

declare(strict_types=1);

namespace fwc\Thing;

class ImageObjectGet  extends ThingGetAbstract implements ThingGetInterface
{
    protected $table = "imageObject";
    protected $type = "ImageObject";
    protected $hasProperties = [ "src", "keywords", "position", "representativeOfPage", "caption" ];
    
    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null): string
    {
        return parent::index($where, $orderBy, $groupBy, $limit, $offset);
    }


    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) 
    {
        $data = parent::listAll($where, $order, $limit, $offset);
    }
    
    public function getHasPart($tableOwner, $idOwner, $orderBy = null, $groupby = null) 
    {
        $orderBy = $orderBy ?? "position";
        
        return parent::getHasPart($tableOwner, $idOwner, $orderBy, $groupby);
    }
    
    public function representativeOfPage($tableOwner, $idOwner, $order = null) 
    {
        $data = parent::getHasPart($tableOwner, $idOwner, $order);
        
        if (empty($data)) {
            return null;
            
        } else {
            foreach ($data as $valueImage) {
                if ($valueImage['representativeOfPage']) {
                    $image =  $this->httpRoot.$valueImage['contentUrl'];
                    break;
                    
                } else {
                    $images[] = $valueImage['contentUrl'];
                }
            }
            
            return json_encode($image ?? $images[0]);
        }
    }
    
    public static function getRepresentativeImageOfPage($data, $mode = "string") 
    {
        if ($data) {
            foreach ($data as $valueImage) {
                if (isset($valueImage['representativeOfPage'])) {
                    $image =  $valueImage['url'];
                    $arrayRep = $valueImage;
                    break;
                    
                } else {
                    $images[] = $valueImage['url'];
                    $array[] = $valueImage;
                }
            }   
            
            if ($mode == "string") {
                return $image ?? $images[0] ?? null;
                
            } else {
                return $arrayRep ?? $array[0];
            }
        }
    }
    
    public function getPrimaryImageOfTableOwner($tableOwner, $idOwner) 
    {
        $data = (new ImageObjectModel())->getPrimaryImageOfTableOwner($tableOwner, $idOwner);
        return empty($data) ? null : json_encode(self::schema($data[0]));
    }
    
    public function getKeywords($group = null) 
    {
        $data = (new ImageObjectModel())->getKeywords($group);
        
        foreach ($data as $value) {
            if ($value['keywords']) {
                $list[] = $value['keywords'];
            }
        }
        
        return json_encode(ItemList::list(count($data), $list));
    }
    
    public function getImagesFromGroup($group) {
        $data = (new ImageObjectModel())->getKeywords($group);        
        foreach ($data as $value) {
            $list[] = self::completeSchema($value);
        }        
        return json_encode([
            "@context" => "https://schema.org",
            "@type" => "ItemList",
            "numberOfItems" => count($data),
            "itemListElement" => $list ?? null
        ]);
    }
    
    protected function schema($value) 
    {        
        $this->setSchema("identifier", [
            [ "@type" => "PropertyValue", "name" => "fwc_id", "value" => $value['idimageObject'] ]
        ]);
        // url
        $this->setSchema("url", $value['contentUrl']);
        
        // thumbnail
        $thumbnail = new \fwc\Html\Object\ThumbnailObject($value['contentUrl']);
        $thumb = $thumbnail->getThumbnail(200);
        $this->setSchema('thumbnail', $thumb);
        
        // sizes
        $imagePath = $_SERVER['DOCUMENT_ROOT'].$value['contentUrl'];
        if (file_exists($imagePath)) {
            list( $width, $height) = getimagesize($_SERVER['DOCUMENT_ROOT'].$value['contentUrl']);
            $this->setSchema('width', $width);
            $this->setSchema('height', $height);
        }
        
        $this->getSchema($value);
        
        return $this->schema;
    }
}
