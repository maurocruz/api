<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue;

use Plinct\Api\Domain\Entities\Thing\Thing;

class GeoCoordinates extends Thing
{
	public const IS_MODULE = false;
	public const DEPENDENCIES = ['PostalAddress'];

	protected ?int $idgeoCoordinates;
	public mixed $address = null;
	public ?string $elevation = null;
	public ?float $latitude = null;
	public ?float $longitude = null;
	public array $identifier = [];

	public function __construct(array $value = ['@type' => 'GeoCoordinates'])
	{
		parent::__construct(['@type' => 'GeoCoordinates']);

		$this->elevation = $value['elevation'] ?? null;
		$this->latitude = $value['latitude'] ?? null;
		$this->longitude = $value['longitude'] ?? null;
		$this->address = isset($value['idpostalAddress']) ? new PostalAddress($value) : $value['address'] ?? null;
		$this->idgeoCoordinates = $value['idgeoCoordinates'] ?? null;
	}

	public function getId(): int
	{
		return $this->idgeoCoordinates;
	}
}
