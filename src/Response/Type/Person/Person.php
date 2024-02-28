<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type\Person;

use Plinct\Api\Response\Type\TypeAbstract;

class Person extends TypeAbstract
{
	/**
	 * @var array|null
	 */
	private ?array $value;

	/**
	 * @param array|null $value
	 * @return void
	 */
	public function get(?array $value): void
	{
		$this->setIdentifier('idperson', (string) $value['idperson']);
		if (isset($value['thing'])) {
			$this->setThingData($value['thing']);
		}
		unset($value['idperson']);
		unset($value['thing']);
		$this->value = $value;
	}

	/**
	 * @return array
	 */
	public function ready(): array
	{
		return array_merge($this->contextSchema, $this->thingData, $this->value, ['identifier'=>$this->identifier]);
	}
}
