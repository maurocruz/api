<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\ContactPoint;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint\ContactPointCreateUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint\ContactPointReadUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class ContactPointController
{
	public function __construct(
		private ContactPointReadUseCase $contactPointReadUseCase,
		private ContactPointCreateUseCase $contactPointCreateUseCase,
	) {}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$dataUseCase = $this->contactPointReadUseCase->read($queryParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parseBody = $request->getParsedBody();
		$responseData = $this->contactPointCreateUseCase->create($parseBody);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
