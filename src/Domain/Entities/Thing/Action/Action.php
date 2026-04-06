<?php
namespace Plinct\Api\Domain\Entities\Thing\Action;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Action extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	public int $idaction;
	protected int $thing;
	public ?string $actionProcess = null;
	public string $actionStatus;
	public ?int $agent = null;
	public ?string $endTime = null;
	public mixed $object = null;
	public ?int $provider = null;
	public ?string $result = null;
	public string $startTime;
	public ?string $targetCollection = null;

	public static function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
