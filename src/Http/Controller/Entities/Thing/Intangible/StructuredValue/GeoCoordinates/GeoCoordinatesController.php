<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\GeoCoordinates;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\GeoCoordinates\GeoCoordinatesUpdateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class GeoCoordinatesController
{
	public function __construct(
		private GeoCoordinatesUpdateUseCase $geoCoordinatesUpdateUseCase
	)	{ }

	/**
	 * @throws ReflectionException
	 */
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$useCaseData = $this->geoCoordinatesUpdateUseCase->update($parsedBody);
		$response->getBody()->write(json_encode(SupportApplication::returnDTOFromDatabaseData($useCaseData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response->withHeader('Content-Type', 'application/json');
	}
}
