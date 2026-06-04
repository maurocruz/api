<?php
namespace Plinct\Api\Domain\Entities\User;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class User implements EntityInterface
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	protected int $iduser;
	public string $name;
	public string $email;
	protected string $password;
	public string $dateCreated;
	public string $dateModified;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}

	public function getType(): string
	{
		return 'User';
	}

	public function getId(): int
	{
		return $this->iduser;
	}
}
