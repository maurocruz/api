<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

class PostalAddress extends ContactPoint
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	public ?string $streetAddress = null;
	public ?string $addressLocality = null;
	public ?string $addressRegion = null;
	public ?string $addressCountry = null;
	public ?string $postalCode = null;
	protected ?int $idpostalAddress;

	public function __construct(array $value = ['@type' => 'PostalAddress'])
	{
		parent::__construct(['@type' => 'PostalAddress']);

		$this->idpostalAddress = $value['idpostalAddress'] ?? null;
		$this->streetAddress = $value['streetAddress'] ?? null;
		$this->addressLocality = $value['addressLocality'] ?? null;
		$this->addressRegion = $value['addressRegion'] ?? null;
		$this->addressCountry = $value['addressCountry'] ?? null;
		$this->postalCode = $value['postalCode'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idpostalAddress;
	}
}
