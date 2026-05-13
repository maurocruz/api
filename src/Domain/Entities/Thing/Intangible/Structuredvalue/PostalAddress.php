<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class PostalAddress implements EntityInterface
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	protected int $idpostalAddress;
	public ?string $streetAddress = null;
	public ?string $addressLocality = null;
	public ?string $addressRegion = null;
	public ?string $addressCountry = null;
	public ?string $postalCode = null;

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getType(): string
	{
		return 'PostalAddress';
	}

	public function getId(): int
	{
		return $this->idpostalAddress;
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}
}
