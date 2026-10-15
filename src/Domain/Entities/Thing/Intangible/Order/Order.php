<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Order;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Order extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idorder;
	public mixed $acceptedOffer;
	public mixed $customer;
	public mixed $seller;
	public ?string $orderDate = null;
	public ?string $paymentDueDate = null;
	public ?string $tags = null;
	public mixed $orderedItem;
	public ?string $orderStatus = null;
	public ?int $discount = null;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->acceptedOffer = $value['acceptedOffer'] ?? null;
		$this->customer = $value['customer'] ?? null;
		$this->seller = $value['seller'] ?? null;
		$this->orderDate = $value['orderDate'] ?? null;
		$this->paymentDueDate = $value['paymentDueDate'] ?? null;
		$this->tags = $value['tags'] ?? null;
		$this->orderedItem = $value['orderedItem'] ?? null;
		$this->orderStatus = $value['orderStatus'] ?? null;
		$this->discount = $value['discount'] ?? null;
	}

	public function getId(): int
	{
		return $this->idorder;
	}
}
