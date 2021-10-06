<?php

declare(strict_types=1);

namespace Plinct\Api\Server;

use Plinct\Api\Server\ClassHierarchy\ClassHierarchy;

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
}