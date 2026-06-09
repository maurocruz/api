<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class OrderItem implements EntityInterface
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','Offer','Order'];

	protected int $idorderItem;
	public int $orderItemNumber;
	public int $offer;
	public mixed $orderedItem; // TODO alterar para acceptedOffer
	public int $orderQuantity;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getType(): string
	{
		return 'OrderItem';
	}

	public function getId(): int
	{
		return $this->idorderItem;
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}
}
