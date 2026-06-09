<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Offer;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Offer extends Thing
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idoffer;
	protected int $thing;
	public mixed $itemOffered;
	public mixed $offeredBy;
	public float $price;
	public string $priceCurrency = 'BRL';
	public ?string $validThrough = null;
	public ?string $availability = null;
	public ?string $eligibleQuantity = null;
	public ?string $eligibleDuration = null;

	public function getType(): string
	{
		return 'Offer';
	}

	public function getId(): int
	{
		return $this->idoffer;
	}
}
