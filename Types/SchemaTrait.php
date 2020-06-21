<?php

namespace Fwc\Api\Type;


trait SchemaTrait 
{
    protected static function listItem(array $list = null): array 
    {
        if (!$list) {
            $name = "Empty list";
            $numberOfItems = 0;
        }
                
        $value = [
            "@type" => "ItemList",
            "name" => $name,
            "numberOfItems" => $numberOfItems,
            "itemListOrder" => $itemListOrder ?? null,
            "itemListElement" => $itemListElement ?? null
        ];        
        
        return self::schema($value);
    }
    
    private static function schema(array $value) 
    {
        $schema = [
            "@context" => "https://schema.org",
            "@type" => $value
        ];
        return $schema;
    }
}
