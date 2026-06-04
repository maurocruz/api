<?php
namespace Plinct\Api\Domain\Entities\User;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class User_history implements EntityInterface
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['User'];

	protected int $iduser_history;
	protected ?int $iduser = null;
	protected string $method;
	protected ?string $summary = null;
	protected string $targetTable;
	protected int $targetId;

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
		return 'User_history';
	}

	public function getId(): int
	{
		return $this->iduser_history;
	}
}
