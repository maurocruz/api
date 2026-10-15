<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

use Plinct\Api\Domain\Entities\Thing\Thing;

class PropertyValue extends Thing
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idpropertyValue;
	public ?string $value = null;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->value = $value['value'] ?? null;
	}

	public function getId(): int
	{
		return $this->idpropertyValue;
	}
}
