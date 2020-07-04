<?php

namespace Fwc\Api\Type;

class Event extends TypeAbstract implements TypeInterface
{
    protected $table = "event";
    
    protected $type = "Event";
    
    protected $properties = [ "name", "location", "startDate" ];
    
    protected $withTypes = [ "location" => "Place", "image" => "ImageObject" ];
    
    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    public function post(array $params): array 
    {
        return parent::post($params);
    }
    
    public function put(string $id, $params): array 
    {
        return parent::put($id, $params);
    }
    
    public function delete(string $id, $params): array 
    {
        return parent::delete($id, $params);
    }
    
    public function createSqlTable($type = null) 
    {
        return parent::createSqlTable($type);
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
