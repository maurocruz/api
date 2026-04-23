<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class CreativeWork extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing'];

	protected int $idcreativeWork;
	protected int $thing;
	public ?int $about = null;
	public ?string $acquireLicensePage = null;
	public ?string $alternativeHeadline = null;
	public ?string $author = null;
	public ?string $copyrightHolder = null;
	public ?string $creativeWorkStatus = null;
	public ?string $datePublished = null;
	public ?string $editor = null;
	public ?string $encodingFormat = null;
	public mixed $hasPart = null;
	public ?string $headline = null;
	public mixed $isPartOf = null;
	public string $keywords = '';
	public ?string $licence = null;
	public ?string $locationCreated = null;
	public ?string $maintainer = null;
	public ?int $position = null;
	public ?string $publisher = null;
	public ?string $size = null;
	public ?string $text = null;
	public ?string $thumbnail = null;
	public string $version = '';


	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
