<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

use Plinct\Api\Domain\Entities\Thing\Thing;

class ContactPoint extends Thing
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['Thing'];

	private ?int $thing;
	protected ?int $idcontactPoint;
	public ?string $contactOption = null;
	public ?string $contactType = null;
	public ?string $telephone = null;
	public ?string $email = null;

	public function __construct(array $value = ["@type" => "Thing"])
	{
		parent::__construct($value);

		$this->idcontactPoint = $value['idcontactPoint'] ?? null;
		$this->contactOption = $value['contactOption'] ?? null;
		$this->contactType = $value['contactType'] ?? null;
		$this->email = $value['email'] ?? null;
		$this->telephone = $value['telephone'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idcontactPoint;
	}
}
