<?php
namespace Plinct\Api\Http\Controller\Entities;

use Plinct\Api\Application\Entities\EntityDeleteUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class EntityDeleteController
{
	public function __construct(
		private EntityDeleteUseCase $entityDeleteUseCase
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type') ?? null;
		$queryParams = $request->getQueryParams();
		$responseData = $this->entityDeleteUseCase->delete($type, $queryParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

}
