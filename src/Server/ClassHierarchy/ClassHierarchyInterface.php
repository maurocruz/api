<?php

namespace Plinct\Api\Server\ClassHierarchy;

interface ClassHierarchyInterface
{
    /**
     * @return array
     */
    public function ready(): array;
}