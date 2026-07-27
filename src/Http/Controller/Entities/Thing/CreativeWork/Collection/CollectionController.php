<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\Collection;

use Plinct\Api\Application\Entities\Thing\CreativeWork\Collection\CollectionCreateUseCase;
use Plinct\Api\Application\Entities\Thing\CreativeWork\Collection\CollectionReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class CollectionController
{
	public function __construct(
		private CollectionReadUseCase $collectionReadUseCase,
		private CollectionCreateUseCase $collectionCreateUseCase
 	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('Collection', $queryParams);
		$dataUseCase = SupportController::returnDTO($this->collectionReadUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams('Collection', $parsedBody);
		$databaseData = SupportController::returnDTO($this->collectionCreateUseCase->create($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($databaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
