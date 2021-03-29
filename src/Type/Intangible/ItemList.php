<?php
namespace Plinct\Api\Type\Intangible;
/**
 * ItemList
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ItemList {
    
    static public function list(int $numberOfItems = 0, array $itemListElement = null, $itemListOrder = null, $name = null, $type = "ItemList"): array {
        if ($itemListElement) {
            $i = 1;
            foreach ($itemListElement as $value) {
                $list[] = [
                    "@type" => "ListItem",
                    "position" => $i,
                    "item" => $value
                ];
                $i++;
            }
        }
        return [
            "@context" => "https://schema.org",
            "@type" => $type,
            "name" => $name,
            "numberOfItems" => $numberOfItems,
            "itemListOrder" => $itemListOrder,
            "itemListElement" => $list ?? null
        ];
    }
}
