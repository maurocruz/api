<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\Offer;

use Plinct\Api\Application\Entities\Thing\Intangible\Offer\OfferReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class OfferController
{
	public function __construct(
		private OfferReadUseCase $offerReadUseCase,
	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('Offer', $queryParams);
		$dataUseCase = $this->offerReadUseCase->read($sanitizedParams);
		$dto = SupportController::returnDTO($dataUseCase);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
