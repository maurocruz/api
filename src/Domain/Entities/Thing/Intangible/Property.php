<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class Property implements EntityInterface
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	protected int $idproperty;
	protected int $idHasPart;
	protected int $idIsPartOf;
	protected string $typeHasPart;
	protected string $typeIsPartOf;
	public ?string $caption = null;
	public ?int $position = 1;
	public int $representativeOfPage = 0;
	protected int $repUniqueFlag = 0;

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
		return 'Property';
	}

	public function getId(): int
	{
		return $this->idproperty;
	}
}
