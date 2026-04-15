<?php
namespace Plinct\Api\Http\Controller\Entities;

use Plinct\Api\Application\Entities\EntityReadUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class EntityReadController
{
	public function __construct(
		private EntityReadUseCase $entityReadUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type');
		$queryParams = $request->getQueryParams();
		$dataUseCase = $this->entityReadUseCase->read($type, $queryParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
