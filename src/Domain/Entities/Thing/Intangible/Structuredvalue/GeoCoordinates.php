<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Contracts\EntityInterface;

class GeoCoordinates implements EntityInterface
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = ['PostalAddress'];

	protected int $idgeoCoordinates;
	public mixed $address = null;
	public ?string $elevation = null;
	public ?float $latitude = null;
	public ?float $longitude = null;
	public ?string $type = 'GeoCoordinates';
	public array $identifier = [];

	public function __construct(array $value = ['@type' => 'GeoCoordinates'])
	{
		$this->idgeoCoordinates = $value['idgeoCoordinates'] ?? 0;
		$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idgeoCoordinates', 'value' => $this->idgeoCoordinates];
		$this->elevation = $value['elevation'] ?? null;
		$this->latitude = $value['latitude'] ?? null;
		$this->longitude = $value['longitude'] ?? null;

		if(isset($value['idpostalAddress'])) {
			$this->address = new PostalAddress($value);
		} else {
			$this->address = $value['address'] ?? null;
		}
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getDependencies(): array
	{
		return self::DEPENDENCIES;
	}

	public function getId(): int
	{
		return $this->idgeoCoordinates;
	}
}
