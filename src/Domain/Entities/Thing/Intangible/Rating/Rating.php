<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Rating;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\Thing;

class Rating extends Thing
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	public ?float $ratingValue = null;
	public ?string $reviewAspect = null;

	public function __construct(array $value = ['@type' => 'Rating'])
	{
		parent::__construct($value);

		$this->ratingValue = $value['ratingValue'] ?? null;
		$this->reviewAspect = $value['reviewAspect'] ?? null;
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
