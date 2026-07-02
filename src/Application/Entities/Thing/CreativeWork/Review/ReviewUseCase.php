<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\Review;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkCreateUseCase;

class ReviewUseCase extends CreativeWorkCreateUseCase
{

	public function create(array $sanitizedParams): array
	{
		$sanitizedParams['type'] = 'Review';
		$itemReviewed = $sanitizedParams['itemReviewed'] ?? null;
		$reviewRating = $sanitizedParams['reviewRating'] ?? null;
		$reviewAspect = $sanitizedParams['reviewAspect'] ?? null;
		$author = $sanitizedParams['author'] ?? null;
		if (!$itemReviewed || !$reviewRating || !$author || !$reviewAspect) {
			return ['status' => false, 'message' => 'Missing required fields','data' => $sanitizedParams];
		}
		$sanitizedParams['name'] = $sanitizedParams['name'] ?? 'Review: '. $reviewAspect;

		// SALVAR CREATIVE WORK
		$dataCreatedCreativeWork = parent::create($sanitizedParams);
		if ($dataCreatedCreativeWork['status'] === false) {
			return $dataCreatedCreativeWork;
		}
		$valueCreativeWork = $dataCreatedCreativeWork['data'][0];
		$sanitizedParams['creativeWork'] = $valueCreativeWork['idcreativeWork'];
		$sanitizedParams['thing'] = $valueCreativeWork['thing'];

		// SALVAR REVIEW
		return $this->databaseActions->create('review', $sanitizedParams);
	}
}
