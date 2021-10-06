<?php

declare(strict_types=1);

namespace Plinct\Api\Server;

use Plinct\Api\Type\TypeInterface;

class Request
{
    /**
     * @var TypeInterface|mixed
     */
    private TypeInterface $typeObject;
    /**
     * @var array
     */
    private array $data;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
        $this->typeObject = new $className();
    }

    /**
     * @param array $params
     * @return $this
     */
    public function get(array $params): Request
    {
        $this->data = $this->typeObject->get($params);
        return $this;
    }

    /**
     * @return array
     */
    public function ready(): array
    {
        return $this->data;
    }
}
