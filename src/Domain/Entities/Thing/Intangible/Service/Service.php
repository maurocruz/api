<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Service;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Service extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	private ?int $thing;
	protected ?int $idservice;
	public mixed $provider = null;
	public ?string $category = null;
	public ?string $serviceType = null;
	public ?string $termsOfService = null;
	public ?int $isRelatedTo = null;
	public ?int $serviceOutput = null;

	public function __construct(array $value = ["@type" => "Service"])
	{
		parent::__construct($value);

		$this->idservice = $value['idservice'] ?? null;
		$this->provider = $value['provider'] ?? null;
		$this->category = $value['category'] ?? null;
		$this->serviceType = $value['serviceType'] ?? null;
		$this->termsOfService = $value['termsOfService'] ?? null;
		$this->isRelatedTo = $value['isRelatedTo'] ?? null;
		$this->serviceOutput = $value['serviceOutput'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idservice;
	}
}
