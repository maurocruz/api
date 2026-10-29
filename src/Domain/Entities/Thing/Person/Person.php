<?php
namespace Plinct\Api\Domain\Entities\Thing\Person;

use Plinct\Api\Domain\Entities\Thing\Intangible\Role\Role;
use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\ContactPoint;
use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\PostalAddress;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Person extends Thing
{
	public const IS_MODULE = true;
	public const DEPENDENCIES = ['Thing','Place','PostalAddress'];

	private ?int $thing;
	protected ?int $idperson;
	public ?string $additionalName = null;
	public mixed $address = null;
	public ?string $birthDate = null;
	public ?string $birthPlace = null;
	public mixed $contactPoint = null;
	public ?string $deathDate = null;
	public ?string $deathPlace = null;
	public ?string $familyName = null;
	public ?string $hasOccupation = null;
	public ?int $homeLocation = null;
	public ?string $gender = null;
	public ?string $givenName = null;
	public mixed $memberOf = null;

	public function __construct(array $value = ["@type" => "Person"])
	{
		parent::__construct($value);

		$this->idperson = $value['idperson'] ?? null;
		$this->additionalName = $value['additionalName'] ?? null;
		$this->address = isset($value['idpostalAddress']) ? new PostalAddress($value) : $value['address'] ?? null;
		$this->birthDate = $value['birthDate'] ?? null;
		$this->birthPlace = $value['birthPlace'] ?? null;
		$this->contactPoint = isset($value['contactPoint']) && is_array($value['contactPoint']) ? array_map(fn($contactPoint) => new ContactPoint($contactPoint), $value['contactPoint']) : null;
		$this->deathDate = $value['deathDate'] ?? null;
		$this->deathPlace = $value['deathPlace'] ?? null;
		$this->familyName = $value['familyName'] ?? null;
		$this->hasOccupation = $value['hasOccupation'] ?? null;
		$this->homeLocation = $value['homeLocation'] ?? null;
		$this->gender = $value['gender'] ?? null;
		$this->givenName = $value['givenName'] ?? null;
		$this->memberOf = isset($value['memberOf']) && is_array($value['memberOf']) ? array_map(fn($role) => new Role($role), $value['memberOf']) : null;
	}

	public function getId(): ?int
	{
		return $this->idperson;
	}
}
