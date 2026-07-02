<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Review;

use Plinct\Api\Application\Support\EntityReflections;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Review extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	protected int $idreview;
	protected int $thing;
	protected int $creativeWork;
	public int $itemReviewed;
	public ?string $reviewAspect = null;
	public ?string $reviewBody = null;
	public int $reviewRating = 0;

	public function __construct(array $value = ['@type' => 'Review'])
	{
		$this->idreview = $value['idreview'] ?? 0;
		if ($this->idreview !== 0) {
			$this->identifier[] = ['@type' => 'PropertyValue', 'name' => 'idreview', 'value' => $this->idreview];
		}
		$this->type = 'Review';

		parent::__construct($value);

		$this->thing = $value['thing'] ?? 0;
		$this->itemReviewed = $value['itemReviewed'] ?? 0;
		$this->reviewAspect = $value['reviewAspect'] ?? null;
		$this->reviewBody = $value['reviewBody'] ?? null;
		$this->reviewRating = $value['reviewRating'] ?? 0;
	}

	public function hydrateParams(array $data, bool $withParent = false): array
	{
		$reflection = new EntityReflections(self::class);
		return $reflection->hydrateOwnProperties($data);
	}
}
