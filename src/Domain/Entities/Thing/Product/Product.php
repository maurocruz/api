<?php
namespace Plinct\Api\Domain\Entities\Thing\Product;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Product extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	private ?int $thing;
	protected int $idproduct;
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

	public function __construct(array $value = ["@type" => "Product"])
	{
		parent::__construct($value);

		$this->idproduct = $value['idproduct'];
		$this->brand = $value['brand'] ?? null;
		$this->category = $value['category'] ?? null;
		$this->color = $value['color'] ?? null;
		$this->keywords = $value['keywords'] ?? null;
		$this->manufacturer = $value['manufacturer'] ?? null;
		$this->material = $value['material'] ?? null;
		$this->model = $value['model'] ?? null;
		$this->mpn = $value['mpn'] ?? null;
		$this->productionDate = $value['productionDate'] ?? null;
		$this->productId = $value['productId'] ?? null;
		$this->purchaseDate = $value['purchaseDate'] ?? null;
	}
	public function getId(): ?int
	{
		return $this->idproduct;
	}
}
