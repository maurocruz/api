<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\Request\Server\Entity;

class PropertyValue extends Entity
{
	public function __construct()
	{
		$this->setTable('propertyValue');
	}
}
