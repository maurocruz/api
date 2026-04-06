<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Book;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Book extends CreativeWork
{
	public const bool IS_MODULE = true;

	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	protected int $idbook;
	protected int $thing;
	protected int $creativeWork;
	public ?string $bookFormat = null;
	public ?int $illustrator = null;
	public ?string $isbn = null;
	public ?string $bookEdition = null;
	public ?string $numberOfPages = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
