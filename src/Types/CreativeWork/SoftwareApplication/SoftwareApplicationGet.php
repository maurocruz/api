<?php

namespace fwc\Thing;

/**
 * SoftwareApplicationGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

class SoftwareApplicationGet implements ThingGetInterface
{
    public function listAll($search = null, $order = null, $limit = null, $offset = null) 
    {
        
    }
    
    public function selectById($id) 
    {
        $value['name'] = "FWC Data Schema";
        return json_encode(self::schema($value), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
    
    
    private static function schema($value) 
    {
        return [
            "@context" => "https://schema.org",
            "type" => "SoftwareApplication",
            "name" => $value['name'],
            "author" => "Mauro Cruz",
            "permissions" => [ "userpass" ],
            "user" => $value['user'] ?? null
        ];
    }
}
