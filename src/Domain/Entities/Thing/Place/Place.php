<?php
namespace Plinct\Api\Domain\Entities\Thing\Place;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\GeoCoordinates;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Place extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','GeoCoordinates'];

	protected int $idplace;
	protected int $thing;
	public ?string $keywords = null;
	public bool $publicAccess = false;
	public mixed $geo = null;
	public ?array $aggregateRating = null;

	public function __construct(array $value = ['@type' => 'Place'])
	{
		$this->idplace = $value['idplace'] ?? 0;
		$this->type = 'Place';
		$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idplace', 'value' => $this->idplace];

		parent::__construct($value);

		$this->thing = $value['thing'] ?? 0;
		$this->publicAccess = isset($value['publicAccess']);
		$this->keywords = $value['keywords'] ?? null;

		// GEO COORDINATES
		if(isset($value['idgeoCoordinates'])) {
			$this->geo = new GeoCoordinates($value);
		} else {
			$this->geo = $value['geo'] ?? null;
		}

		// AGGREGATE RATING
		if($value['idreview'] ?? null) {
			$this->aggregateRating = [
				'@type' => 'AggregateRating',
				'ratingValue' => $value['ratingValue'] ?? null,
				'reviewCount' => $value['reviewCount'] ?? null,
			];
		}
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data, $withParent);
	}
}
