<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage\WebPageCreateUseCase;
use Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage\WebPageReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class WebPageController
{
	public function __construct(
		private WebPageReadUseCase   $webPageUseCase,
		private WebPageCreateUseCase $webPageCreateUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('WebPage', $queryParams);
		$responseRead = SupportController::returnDTO($this->webPageUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseRead, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parsedBody = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams('WebPage', $parsedBody);
		$resultCreate = SupportController::returnDTO($this->webPageCreateUseCase->create($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($resultCreate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
