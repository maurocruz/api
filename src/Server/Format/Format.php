<?php

declare(strict_types=1);

namespace Plinct\Api\Server\Format;

use Plinct\Api\Server\Format\ClassHierarchy\ClassHierarchy;
use Plinct\Api\Server\Format\Geojson\Geojson;

class Format
{
    /**
     * @param string $type
     * @param array $params
     * @return ClassHierarchy
     */
    public static function classHierarchy(string $type, array $params): ClassHierarchy
    {
        return new ClassHierarchy($type, $params);
    }

    /**
     * @param object $objectType
     * @param array $params
     * @return Geojson
     */
    public static function geojson(object $objectType, array $params): Geojson
    {
        return new Geojson($objectType, $params);
    }
}