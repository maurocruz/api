<?php

namespace Plinct\Api\Server\Format\ClassHierarchy;

interface ClassHierarchyInterface
{
    /**
     * @return array
     */
    public function ready(): array;
}