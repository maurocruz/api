<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Order;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class Order implements EntityInterface
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

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getType(): string
	{
		return 'Order';
	}

	public function getId(): int
	{
		return $this->idorder;
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}
}
