<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Trip;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Trip extends Thing
{
	public const IS_MODULE = true;
	public const DEPENDENCIES = ['Thing'];

	private ?int $thing;
	protected ?int $idtrip;
	public ?string $arrivalTime = null;
	public ?string $departureTime = null;
	public int $provider;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->idtrip = $value['idtrip'] ?? null;
		$this->arrivalTime = $value['arrivalTime'] ?? null;
		$this->departureTime = $value['departureTime'] ?? null;
		$this->provider = $value['provider'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idtrip;
	}
}
