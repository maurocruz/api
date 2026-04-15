<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\PropertyValue;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\PropertyValue\PropertyValueCreateUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\PropertyValue\PropertyValueReadUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class PropertyValueController
{
	public function __construct(
		private PropertyValueReadUseCase $propertyValueReadUseCase,
		private PropertyValueCreateUseCase $propertyValueCreateUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$responseData = $this->propertyValueReadUseCase->read($queryParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parseBody = $request->getParsedBody();
		$responseData = $this->propertyValueCreateUseCase->create($parseBody);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
