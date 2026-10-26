<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Role;

use Plinct\Api\Domain\Entities\Thing\Person\Person;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Role extends Thing
{
	public const IS_MODULE = false;
	public const DEPENDENCIES = ['Thing','Person'];

	private ?int $thing;
	protected ?int $idrole;
	protected mixed $person;
	protected mixed $organization;
	public ?string $endDate = null;
	public ?string $startDate = null;
	public mixed $secondaryRole;
	public mixed $member;

	public function __construct(array $value = ["@type" => "Role"])
	{
		parent::__construct($value);

		$this->idrole = $value['idrole'] ?? null;
		$this->endDate = (isset($value['endDate']) && $value['endDate'] === '0000-00-00') ? null : $value['endDate'];
		$this->startDate = $value['startDate'] === '0000-00-00' ? null : $value['startDate'];

		$this->person = $value['person'] ?? null;

		$this->member = (isset($value['member']) && is_array($value['member'])) ? (new Person($value['member']))->jsonSerialize() :  $value['member'] ?? null;

		$this->organization = $value['organization'] ?? null;
		$this->secondaryRole = $value['secondaryRole'] ?? null;
	}

	public function getId(): ?int
	{
		return $this->idrole;
	}
}
