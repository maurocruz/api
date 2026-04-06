<?php
namespace Plinct\Api\Domain\Entities\Thing;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class Thing implements EntityInterface
{
	public const bool IS_MODULE = false;

	public const array DEPENDENCIES = [];

	protected int $idthing;
	public ?string $additionalType = null;
	public ?string $alternateName = null;
	protected string $dateRegistered;
	protected string $lastModified;
	public ?string $description = null;
	public ?string $disambiguatingDescription = null;
	public ?string $image = null;
	public ?string $mainEntityOfPage = null;
	public string $name;
	public ?string $sameAs = null;
	public string $type;
	public ?string $url = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
