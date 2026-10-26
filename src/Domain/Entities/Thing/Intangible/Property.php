<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Property extends Thing
{
	public const IS_MODULE = false;
	public const DEPENDENCIES = [];

	protected int $idproperty;
	protected int $idHasPart;
	protected int $idIsPartOf;
	protected string $typeHasPart;
	protected string $typeIsPartOf;
	public ?string $caption = null;
	public ?int $position = 1;
	public int $representativeOfPage = 0;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->idproperty = $value['idproperty'] ?? null;
		$this->idHasPart = $value['idHasPart'] ?? null;
		$this->idIsPartOf = $value['idIsPartOf'] ?? null;
		$this->typeHasPart = $value['typeHasPart'] ?? null;
		$this->typeIsPartOf = $value['typeIsPartOf'] ?? null;
		$this->caption = $value['caption'] ?? null;
		$this->position = $value['position'] ?? null;
		$this->representativeOfPage = $value['representativeOfPage'] ?? null;
	}

	public function getId(): int
	{
		return $this->idproperty;
	}
}
