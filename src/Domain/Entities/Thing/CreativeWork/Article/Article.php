<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Article;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Article extends CreativeWork
{
	public const bool IS_MODULE = true;

	public const array DEPENDENCIES = ['CreativeWork'];

	protected int $idarticle;
	protected int $thing;
	protected int $creativeWork;
	public ?string $articleBody = null;
	public ?string $articleSection = null;
	public ?string $backstory = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
