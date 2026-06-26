<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Event;

use Plinct\Api\Application\Entities\Thing\Event\EventCreateUseCase;
use Plinct\Api\Application\Entities\Thing\Event\EventReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class EventController
{
	public function __construct(
		private EventReadUseCase $eventReadUseCase,
		private EventCreateUseCase $eventCreateUseCase
	) {}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$dataUseCase = SupportApplication::returnDTOFromDatabaseData($this->eventReadUseCase->read($queryParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody() ?? [];
		$sanitizedParams = SupportApplication::sanitizeParams('Event', $params);
		$dataUseCase = SupportApplication::returnDTOFromDatabaseData($this->eventCreateUseCase->create($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		return $response;
	}
}
