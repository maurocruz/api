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
    $urlArray = array_filter(explode("/",$params['url']));
    $key = count($urlArray);
    $items[] = self::item($key, $params['url'], $params['alternativeHeadline']);
    if ($key > 1) {
      end($urlArray);
      while(current($urlArray)) {
        array_pop($urlArray);
        if(!empty($urlArray)) {
          $items[] = self::getNewParams($urlArray);
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
   * @param $urlArray
   * @return array
   */
  private static function getNewParams($urlArray): array
  {
    $parentUrl = DIRECTORY_SEPARATOR . implode("/", $urlArray);
    $newParams = ["url" => $parentUrl, "properties" => "alternativeHeadline"];
    $parentData = (new WebPage())->get($newParams);
    return self::item(count($urlArray), $parentUrl, $parentData[0]['alternativeHeadline']);
  }

  /**
   * @param $position
   * @param $url
   * @param $alternativeHeadline
   * @return array
   */
  private static function item($position, $url, $alternativeHeadline): array
  {
    return [
	    "@type" => "ListItem",
      "position" => $position,
      "item" => [
        "@id" => $url,
        "name" => $alternativeHeadline
      ]
    ];
  }
}
