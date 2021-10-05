<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use ReflectionException;

class WebSite extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected string $table = 'webSite';
    /**
     * @var string
     */
    protected string $type = 'WebSite';
    /**
     * @var array|string[]
     */
    protected array $properties = ['name'];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = ['hasPart'=>'WebPage'];

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("organization");
        $message[] = $maintenance->createSqlTable("person");
        $message[] = $maintenance->createSqlTable("ImageObject");
        $message[] =  parent::createSqlTable("WebSite");
        $message[] = $maintenance->createSqlTable('WebPage');
        $message[] = $maintenance->createSqlTable('WebPageElement');
        return $message;
    }
}
