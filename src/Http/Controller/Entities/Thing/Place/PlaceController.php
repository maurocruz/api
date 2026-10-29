<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Place;

use Plinct\Api\Application\Entities\Thing\Place\PlaceReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class PlaceController
{
	public function __construct(
		private PlaceReadUseCase $placeReadUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('Place', $queryParams);
		$dataUseCase = SupportController::returnDTO($this->placeReadUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
