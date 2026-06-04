<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\GeoCoordinates;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\GeoCoordinates\GeoCoordinatesCreateUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\GeoCoordinates\GeoCoordinatesReadUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\GeoCoordinates\GeoCoordinatesUpdateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class GeoCoordinatesController
{
	public function __construct(
		private GeoCoordinatesReadUseCase $geoCoordinatesReadUseCase,
		private GeoCoordinatesUpdateUseCase $geoCoordinatesUpdateUseCase,
		private GeoCoordinatesCreateUseCase $geoCoordinatesCreateUseCase,
	)	{ }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$useCaseData = $this->geoCoordinatesReadUseCase->read($queryParams);
		$response->getBody()->write(json_encode(SupportApplication::returnDTOFromDatabaseData($useCaseData), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response->withHeader('content-type', 'application/json');
	}
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

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$dataUseCase = $this->geoCoordinatesCreateUseCase->create($parsedBody);
		$response->getBody()->write(json_encode(SupportApplication::returnDTOFromDatabaseData($dataUseCase), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response->withHeader('Content-Type', 'application/json');
	}
}
