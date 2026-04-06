<?php
namespace Plinct\Api\Http\Controller\Entities;

use Plinct\Api\Application\Entities\EntityUpdateUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class EntityUpdateController
{
	public function __construct(
		private EntityUpdateUseCase $entityUpdateUseCase
	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type') ?? null;
		$params = $request->getParsedBody() ?? [];
		$responseData = $this->entityUpdateUseCase->update($type, $params);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
