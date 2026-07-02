<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPageElement;

use Plinct\Api\Application\Entities\Thing\CreativeWork\WebPageElement\WebPageElementCreateUseCase;
use Plinct\Api\Application\Entities\Thing\CreativeWork\WebPageElement\WebPageElementReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class WebPageElementController
{
	public function __construct(
		private WebPageElementReadUseCase   $webPageElementUseCase,
		private WebPageElementCreateUseCase $webPageElementCreateUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('WebPageElement', $queryParams);
		$responseRead = SupportController::returnDTO($this->webPageElementUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseRead, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams('WebPageElement', $params);
		$responseCreate = SupportController::returnDTO($this->webPageElementCreateUseCase->create($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseCreate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
