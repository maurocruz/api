<?php
namespace Plinct\Api\Domain\Entities\User;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class User_passwordReset implements EntityInterface
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['User'];

	protected int $iduser;
	protected ?string $selector = null;
	protected ?string $token = null;
	protected ?string $expires = null;

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
		return 'User_passwordReset';
	}

	public function getId(): int
	{
		return $this->iduser;
	}
}
