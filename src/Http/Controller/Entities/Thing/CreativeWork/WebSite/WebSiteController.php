<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Application\Entities\Thing\CreativeWork\WebSite\WebSiteCreateUseCase;
use Plinct\Api\Application\Entities\Thing\CreativeWork\WebSite\WebSiteReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class WebSiteController
{
	public function __construct(
		private WebSiteReadUseCase $webSiteReadUseCase,
		private WebSiteCreateUseCase $webSiteCreateUseCase
	) { }


	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('WebSite', $queryParams);
		$data = $this->webSiteReadUseCase->read($sanitizedParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$data = $this->webSiteCreateUseCase->create($params);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
