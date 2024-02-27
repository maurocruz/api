<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;

class WebSite extends Entity
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
}
