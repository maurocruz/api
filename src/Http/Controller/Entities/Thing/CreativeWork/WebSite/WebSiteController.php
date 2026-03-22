<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Application\Entities\Thing\CreativeWork\WebSite\WebSiteCreateUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class WebSiteController
{
	public function __construct(
		private WebSiteCreateUseCase $webSiteCreateUseCase
	) { }

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
