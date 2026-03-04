<?php
namespace Plinct\Api\Domain\Modules\Thing;

class ThingDomain
{
	private string $name;

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}
}
