<?php

namespace Plinct\Api\Type;

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
        if ($params['tableOwner']) {
            return parent::getWithPartOf($params);
        }
        return parent::get($params);
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
    
    /**
     * PUT
     * @param string $id
     * @param type $params
     * @return array
     */
    public function put(string $id, $params = null): array 
    {
        return parent::put($id, $params);
    }
    
    /**
     * DELETE
     * @param string $id
     * @param type $params
     * @return array
     */
    public function delete(array $params): array 
    {
        return parent::delete($id, $params);
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
}
