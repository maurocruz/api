<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server;

class GenericType extends Entity
{
	public function __construct(string $type)
	{
		$this->setTable($type);
	}
}