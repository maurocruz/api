<?php
namespace Plinct\Api\Domain\Entities\Thing\Organization;

class LocalBusiness extends Organization
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','Organization','Place'];

	private ?int $thing;
	protected ?int $idlocalBusiness;
	public ?string $openingHours = null;
	public ?string $paymentAccepted = null;

	public function __construct(array $value = ['@type' => 'LocalBusiness'])
	{
		parent::__construct($value);

		$this->openingHours = $value['openingHours'] ?? null;
		$this->paymentAccepted = $value['paymentAccepted'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idlocalBusiness;
	}
}
