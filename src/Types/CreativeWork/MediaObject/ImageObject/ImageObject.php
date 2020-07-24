<?php

namespace Plinct\Api\Type;

use Plinct\Tool\Thumbnail;

class ImageObject extends TypeAbstract implements TypeInterface
{
    protected $table = "imageObject";
    
    protected $type = "ImageObject";
    
    protected $properties = [ "*" ]; 

    /**
     * PUT
     * @return array
     */
    public function get(array $params): array
    {
        if (isset($params['tableOwner'])) {             
            return parent::getWithPartOf($params);
        }
         
        $data = parent::getData($params);
                
        // EXTRA PROPERTIES
        if (isset($params['properties']) && !array_key_exists('error', $data)) {
            
            // THUMBNAIL
            if (strpos($params['properties'], "thumbnail") !== false) {
                
                foreach ($data as $valueThumb) { 
                    
                    $thumbnail = new Thumbnail($valueThumb['contentUrl']);
                    $valueThumb['thumbnail'] = $thumbnail->getThumbnail(200);

                    $dataThumb[] = $valueThumb;
                }
                $data = $dataThumb;
            }
        
            // SIZES
            if (strpos($params['properties'], 'sizes') !== false) {
                foreach ($data as $valueSizes) {
                    
                    $imagePath = $_SERVER['DOCUMENT_ROOT'].$valueSizes['contentUrl'];
                    
                    if (file_exists($imagePath) && is_file($imagePath)) {
                        
                        list( $width, $height) = getimagesize($imagePath);
                        
                        $valueSizes['width'] = $width;
                        $valueSizes['height'] = $height;
                        
                        $dataSizes[] = $valueSizes;
                    }
                }
                $data = $dataSizes;
            }
        }
        
        return parent::buildSchema($params, $data);
    }
    
    /**
     * POST
     * @param array $params
     * @return array
     */
    public function post(array $params): array 
    {                
        return parent::post($params);
    }
    
    public function postRelationship(array $params) 
    {
        $params['tableIsPartOf'] = $this->table;
        $params['idIsPartOf'] = $params['idimageObject'];
        unset($params['idimageObject']);
                
        return parent::postRelationship($params);
    }
    
    public function newAndPostRelationship($params) 
    {             
        $uploadedFiles = $_FILES['imageupload'];
                                        
        if ($uploadedFiles['size'] !== 0) {
            $tableOwner = $params['tableOwner'];
            unset($params['tableOwner']);
            $idOwner = $params['idOwner'];
            unset($params['idOwner']);        

            // upload image        
            $contentUrl = self::uploadImage($uploadedFiles, $params['location']);

            // insert data image in imageObject table
            $params['contentSize'] = $uploadedFiles['size'];
            $params['contentUrl'] = $params['location'] . DIRECTORY_SEPARATOR . $contentUrl;
            $idimageObject = $this->post($params)['id'];

            // insert relationship
            return parent::createdRelationship($tableOwner, $idOwner, $this->table, $idimageObject);
        }
    }
    
    /**
     * PUT
     * @param string $id
     * @param type $params
     * @return array
     */
    public function put(array $params = null): array 
    {
        return parent::put($params);
    }
    
    /**
     * DELETE
     * @param string $id
     * @param type $params
     * @return array
     */
    public function delete(array $params): array 
    {        
        return parent::delete($params);
    }
    
    public function deleteRelationship($params)
    {
        return parent::eraseRelationship($params['tableOwner'], $params['idOwner'], $this->table, $params['idimageObject']);
    }
    
    /**
     * CREATE SQL
     * @param type $type
     * @return type
     */
    public function createSqlTable($type = null)
    {
        $message[] =  parent::createSqlTable("ImageObject");
        return $message;
    }
    
    
    public static function getRepresentativeImageOfPage($data, $mode = "string") 
    {
        if ($data) {
            foreach ($data as $valueImage) {
                if (isset($valueImage['representativeOfPage'])) {
                    $image =  $valueImage['contentUrl'];
                    $arrayRep = $valueImage;
                    break;
                    
                } else {
                    $images[] = $valueImage['contentUrl'];
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
    
    private static function uploadImage($imageUpload, $location) 
    {
        $dir = substr($location, 0, 1) == '/' ? $location : '/'.$location;
        
        $filename = \Plinct\Tool\StringTool::removeAccentsAndSpaces($imageUpload['name']);
        
        $path = $dir."/".$filename; 
        
        (new \Plinct\Web\Object\ThumbnailObject($imageUpload['tmp_name']))->uploadImage($path);
        
        return $filename;
    }  
}
