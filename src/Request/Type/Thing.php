<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;

class Thing extends Entity
{
    /**
     * @var string
     */
    protected string $table = "thing";
    /**
     * @var string
     */
    protected string $type = "Thing";
	/**
	 * @var array|string[]
	 */
		protected array $properties = ["contactPoint"];
}
