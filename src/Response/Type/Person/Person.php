<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type\Person;

use Plinct\Api\Response\Type\TypeAbstract;

class Person extends TypeAbstract
{
	private ?array $data;

	public function get(?array $data): Person
	{
		$this->data = $data;
		return $this;
	}

	public function ready(): array
	{
		return parent::extractThing($this->data[0]);
	}
}