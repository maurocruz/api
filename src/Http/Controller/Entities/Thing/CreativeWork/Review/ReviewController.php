<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\Review;

use Plinct\Api\Application\Entities\Thing\CreativeWork\Review\ReviewUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class ReviewController
{
	public function __construct(
		private ReviewUseCase $reviewUseCase
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parseBody = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams('Review', $parseBody);
		$dataUseCase = SupportController::returnDTO($this->reviewUseCase->create($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
