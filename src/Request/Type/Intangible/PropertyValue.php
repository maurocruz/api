<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\Request\Server\Entity;
use ReflectionException;

class PropertyValue extends Entity
{
	public function __construct()
	{
		$this->setTable('propertyValue');
	}
}
