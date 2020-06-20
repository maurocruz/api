<?php

namespace fwc\Thing;

/**
 * VideoObjectGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class VideoObjectGet
{
    public function getAll()
    {
        $data = (new VideoObjectModel())->listAll();
        
        //var_dump($data);
        foreach ($data as $key => $value) {
            $list[] = [
                "@type" => "ListItem",
                "position" => ($key+1),
                "item" => self::getItem($value)
            ];
        }
        
        $return = [
            "@type" => "itemList",
            "numberOfItens" => count($data),
            "itemListElement" => $list
        ];
        
        return json_encode($return);
    }
    
    public function getOne($shortName)
    {
        $data = (new VideoObjectModel())->showVideo($shortName);
        $value = $data[0];
        
        return json_encode(self::getItem($value));        
    }
    
    private static function getItem($value)
    {
        return [
            "@type" => "VideoObject",
            "name" => $value['title'],
            "description" => $value['description'],
            "identifier" => $value['id'],
            "keywords" => $value['tag'],
            "thumbnail" => "//".$_SERVER['HTTP_HOST']."/portal/public/images/videos/".$value['thumb'],
            "url" => "//". $_SERVER['HTTP_HOST'] . "/multimidia/video/".$value['url'],
            "contentUrl" => "//".$_SERVER['HTTP_HOST']."/portal/public/images/videos/".$value['url']."_VP8.webm",
            "bitrate" => $value['size'],
            "duration" => $value['length']
        ];
    }
}
