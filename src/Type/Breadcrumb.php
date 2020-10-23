<?php

namespace Plinct\Api\Type;

class Breadcrumb
{
    public function get($params)
    {
        $urlArray = array_filter(explode("/",$params['url']));
        $key = count($urlArray);

        $items[] = self::item($key, $params['url'], $params['alternativeHeadline']);

        if ($key > 1) {
            end($urlArray);
            while($val=current($urlArray)) {
                array_pop($urlArray);
                if(!empty($urlArray)) {
                    $items[] = self::getNewParams($urlArray);
                    end($urlArray);
                } else {
                    break;
                }
            }
        }

        return array_reverse($items);
    }

    private static function getNewParams($urlArray) {
        $parentUrl = DIRECTORY_SEPARATOR . implode("/", $urlArray);
        $newParams = ["url" => $parentUrl, "properties" => "alternativeHeadline"];
        $parentData = (new WebPage())->get($newParams);

        return self::item(count($urlArray), $parentUrl, $parentData[0]['alternativeHeadline']);
    }

    private static function item($position, $url, $alternativeHeadline) {
        return [
            "position" => $position,
            "item" => [
                "id" => $url,
                "name" => $alternativeHeadline
            ]
        ];
    }
}