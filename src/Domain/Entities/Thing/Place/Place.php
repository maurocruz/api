<?php
namespace Plinct\Api\Domain\Entities\Thing\Place;

use Plinct\Api\Domain\Entities\Thing\Intangible\Structuredvalue\GeoCoordinates;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Place extends Thing
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','GeoCoordinates'];

	private ?int $thing;
	public ?string $keywords = null;
	public bool $publicAccess = false;
	public mixed $geo = null;
	public ?array $aggregateRating = null;
	protected ?int $idplace = null;

	public function __construct(array $value = ['@type' => 'Place'])
	{
		parent::__construct($value);

		$this->publicAccess = isset($value['publicAccess']);
		$this->keywords = $value['keywords'] ?? null;
		$this->geo = isset($value['idgeoCoordinates']) ? (new GeoCoordinates($value))->jsonSerialize() : $value['geo'] ?? null;
		$this->idplace = $value['idplace'] ?? 0;
	}

	public function getId(): ?int
	{
		return $this->idplace;
	}
}
