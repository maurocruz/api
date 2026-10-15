<?php
namespace Plinct\Api\Domain\Entities\Thing\Intangible\Rating;

class AggregateRating extends Rating
{
	public const bool IS_MODULE = false;
	public const array DEPENDENCIES = [];

	public mixed $itemReviewed;
	public ?int $ratingCount = null;
	public ?int $reviewCount = null;

	public function __construct(array $value = ['@type' => 'AggregateRating'])
	{
		parent::__construct($value);

		$this->itemReviewed = $value['itemReviewed'] ?? 0;
		$this->ratingCount = $value['ratingCount'] ?? null;
		$this->reviewCount = $value['reviewCount'] ?? null;
	}
}
