<?php
namespace Plinct\Api\Domain\Entities\Thing\Organization;

class LocalBusiness extends Organization
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing','Organization','Place'];

	protected ?int $idlocalBusiness;
	protected int $thing;
	public ?string $openingHours = null;
	public ?string $paymentAccepted = null;
	public mixed $location = null;

	public function __construct(array $value = ['@type' => 'LocalBusiness'])
	{
		$this->idlocalBusiness = $value['idlocalBusiness'] ?? 0;
		if ($this->idlocalBusiness !== 0) {
			$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idlocalBusiness', 'value' => $this->idlocalBusiness];
		}
		$this->type = 'LocalBusiness';

		parent::__construct($value);

		$this->thing = $value['thing'] ?? 0;
		$this->openingHours = $value['openingHours'] ?? null;
		$this->paymentAccepted = $value['paymentAccepted'] ?? null;
	}
}
