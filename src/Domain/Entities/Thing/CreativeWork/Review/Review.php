<?php
namespace Plinct\Api\Domain\Entities\Thing\CreativeWork\Review;

use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;

class Review extends CreativeWork
{
	public const bool IS_MODULE = true;
	public const array DEPENDENCIES = ['Thing','CreativeWork'];

	private ?int $thing;
	protected ?int $idreview;
	protected ?int $creativeWork;
	public int $itemReviewed;
	public ?string $reviewAspect = null;
	public ?string $reviewBody = null;
	public int $reviewRating = 0;

	public function __construct(array $value = ['@type' => 'Review'])
	{
		parent::__construct($value);

		$this->idreview = $value['idreview'] ?? null;
		$this->creativeWork = $value['creativeWork'] ?? null;
		$this->itemReviewed = $value['itemReviewed'] ?? 0;
		$this->reviewAspect = $value['reviewAspect'] ?? null;
		$this->reviewBody = $value['reviewBody'] ?? null;
		$this->reviewRating = $value['reviewRating'] ?? 0;
	}

	public function getId(): ?int
	{
		return $this->idreview;
	}
}
