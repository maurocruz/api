<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

use Plinct\Api\Domain\Entities\Thing\Thing;

class OrderItem extends Thing
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','Offer','Order'];

	protected ?int $idorderItem;
	public int $orderItemNumber;
	public int $offer;
	public mixed $orderedItem; // TODO alterar para acceptedOffer
	public int $orderQuantity;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->idorderItem = $value['idorderItem'] ?? null;
		$this->orderItemNumber = $value['orderItemNumber'] ?? null;
		$this->offer = $value['offer'] ?? null;
		$this->orderedItem = $value['orderedItem'] ?? null;
		$this->orderQuantity = $value['orderQuantity'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idorderItem;
	}
}
