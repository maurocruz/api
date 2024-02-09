<?php

declare(strict_types=1);

namespace Plinct\Api\Type\Intangible;

class ItemList
{
	/**
	 * @param int $numberOfItems
	 * @param array|null $itemListElement
	 * @param $itemListOrder
	 * @param $name
	 * @param string $type
	 * @return array
	 */
  static public function list(int $numberOfItems = 0, array $itemListElement = null, $itemListOrder = null, $name = null, string $type = "ItemList"): array {
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
