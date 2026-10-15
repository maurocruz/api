<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Offer;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Offer extends Thing
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing'];

	private int $thing;
	protected int $idoffer;
	public mixed $itemOffered;
	public mixed $offeredBy;
	public float $price;
	public string $priceCurrency = 'BRL';
	public ?string $validThrough = null;
	public ?string $availability = null;
	public ?string $eligibleQuantity = null;
	public ?string $eligibleDuration = null;

	public function __construct(array $value = ["@type" => "Offer"])
	{
		parent::__construct($value);

		$this->idoffer = $value['idoffer'];
		$this->itemOffered = $value['itemOffered'] ?? null;
		$this->offeredBy = $value['offeredBy'] ?? null;
		$this->price = $value['price'] ?? null;
		$this->priceCurrency = $value['priceCurrency'] ?? 'BRL';
		$this->validThrough = $value['validThrough'] ?? null;
		$this->availability = $value['availability'] ?? null;
		$this->eligibleQuantity = $value['eligibleQuantity'] ?? null;
		$this->eligibleDuration = $value['eligibleDuration'] ?? null;
	}

	public function getId(): int
	{
		return $this->idoffer;
	}
}
