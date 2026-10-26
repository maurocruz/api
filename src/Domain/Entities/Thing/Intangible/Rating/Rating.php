<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Rating;

use Plinct\Api\Domain\Entities\Thing\Thing;

class Rating extends Thing
{
	public const IS_MODULE = false;
	public const DEPENDENCIES = [];

	public ?float $ratingValue = null;
	public ?string $reviewAspect = null;

	public function __construct(array $value = ['@type' => 'Rating'])
	{
		parent::__construct($value);

		$this->ratingValue = $value['ratingValue'] ?? null;
		$this->reviewAspect = $value['reviewAspect'] ?? null;
	}
}
