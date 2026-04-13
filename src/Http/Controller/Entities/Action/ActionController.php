<?php
namespace Plinct\Api\Http\Controller\Entities\Action;

use Plinct\Api\Application\Entities\Thing\Action\ActionCreateUseCase;
use Plinct\Api\Application\Entities\Thing\Action\ActionReadUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class ActionController
{
	public function __construct(
		private ActionReadUseCase $actionReadUseCase,
		private ActionCreateUseCase $actionCreateUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$responseData = $this->actionReadUseCase->read($queryParams);
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
		$useCaseData = $this->actionCreateUseCase->create($parseBody);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
