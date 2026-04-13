<?php
namespace Plinct\Api\Domain\Entities\Thing\Product;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Product extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idproduct;
	protected int $thing;
	public ?string $brand = null;
	public ?string $category = null;
	public ?string $color = null;
	public ?string $keywords = null;
	public ?string $manufacturer = null;
	public ?string $material = null;
	public ?string $model = null;
	public ?string $mpn = null;
	public ?string $productionDate = null;
	public ?string $productId = null;
	public ?string $purchaseDate = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
