<?php
namespace Plinct\Api\Http\Controller\Entities;

use Plinct\Api\Application\Entities\EntityCreateUseCase;
use Plinct\Api\Application\Entities\EntityDeleteUseCase;
use Plinct\Api\Application\Entities\EntityReadUseCase;
use Plinct\Api\Application\Entities\EntityUpdateUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class EntityController
{
	public function __construct(
		private EntityReadUseCase $entityReadUseCase,
		private EntityCreateUseCase $entityCreateUseCase,
		private EntityUpdateUseCase $entityUpdateUseCase,
		private EntityDeleteUseCase $entityDeleteUseCase
	)	{ }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type');
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams($type, $queryParams);
		$dataUseCase =  SupportController::returnDTO($this->entityReadUseCase->read($type, $sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type') ?? null;
		$params = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams($type, $params);
		$dataUseCase = SupportController::returnDTO($this->entityCreateUseCase->createEntity($type, $sanitizedParams));
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type') ?? null;
		$params = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams($type, $params);
		$responseData = SupportController::returnDTO($this->entityUpdateUseCase->update($type, $sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type') ?? null;
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams($type, $queryParams);
		$responseData = SupportController::returnDTO($this->entityDeleteUseCase->delete($type, $sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
