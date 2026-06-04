<?php
namespace Plinct\Api\Domain\Entities\User;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class User_privileges implements EntityInterface
{

	public const bool IS_MODULE = false;

	public const array DEPENDENCIES = ['User'];

	protected int $iduser_privileges;
	protected int $iduser;
	protected int $function = 1;
	protected string $action = 'r';
	protected string $namespace = '';
	protected ?int $userCreator = null;

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
		return 'User_privileges';
	}

	public function getId(): int
	{
		return $this->iduser_privileges;
	}
}
