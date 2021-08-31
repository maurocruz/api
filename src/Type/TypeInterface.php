<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

interface TypeInterface 
{
    /**
     * HTTP Request GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array;

    /**
     * @param array $params
     * @return array
     */
    public function post(array $params): array;

    /**
     * @param array $params
     * @return array
     */
    public function delete(array $params): array;

    /**
     * @param array $params
     * @return array
     */
    public function put(array $params): array;

    /**
     * @param string|null $type
     * @return mixed
     */
    public function createSqlTable(string $type = null): array;
}
