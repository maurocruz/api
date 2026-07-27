<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Collection;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Collection extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	protected int $idcollection;
	private int $thing;
	private int $creativeWork;

	public function __construct(array $value = ["@type" => "Collection"])
	{
		parent::__construct($value);
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
