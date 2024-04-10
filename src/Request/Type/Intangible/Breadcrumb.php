<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\Request\Type\CreativeWork\WebPage;

class Breadcrumb
{
  /**
   * @param $params
   * @return array
   */
  public function get($params): array
  {
		$parseUrl = parse_url($params['url']);
		$baseUrl = $parseUrl['scheme'] . '://' . $parseUrl['host'];
    $urlArray = array_filter(explode("/",$parseUrl['path']));
    $key = count($urlArray);
    $items[] = self::item($key, $params['url'], $params['alternativeHeadline'] ?? $params['alternateName'] ?? $params['name'] ?? null);
    if ($key > 1) {
      end($urlArray);
      while(current($urlArray)) {
        array_pop($urlArray);
        if(!empty($urlArray)) {
          $items[] = self::getNewParams($urlArray, $baseUrl);
          end($urlArray);
        } else {
          break;
        }
      }
    }
		$reverseArray =array_reverse($items);
		return ["@context" => "https://schema.org", "@type" => "BreadcrumbList", "itemListElement" => $reverseArray];
  }

	/**
	 * @param array $urlArray
	 * @param string $baseUrl
	 * @return array
	 */
  private static function getNewParams(array $urlArray, string $baseUrl): ?array
  {
    $parentUrl = $baseUrl . DIRECTORY_SEPARATOR . implode("/", $urlArray);
    $newParams = ["url" => $parentUrl];
    $parentData = (new WebPage())->get($newParams);
		$name = $parentData[0]['alternateName'] ?? $parentData[0]['alternativeHeadline'] ?? null;
    return isset($parentData[0]) ? self::item(count($urlArray), $parentUrl, $name) : null;
  }

	/**
	 * @param $position
	 * @param $url
	 * @param $name
	 * @return array
	 */
  private static function item($position, $url, $name): array
  {
	  $array = explode("/", $url);
	  $name = $name ?? ucfirst(end($array)) ?? '';
    return [
	    "@type" => "ListItem",
      "position" => $position,
      "item" => [
        "@id" => $url,
        "name" => $name
      ]
    ];
  }
}
