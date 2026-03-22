<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage\WebPageCreateUseCase;
use Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage\WebPageReadUseCase;
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
		$responseRead = $this->webPageUseCase->read($queryParams);
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
		$resultCreate = $this->webPageCreateUseCase->create($parsedBody);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($resultCreate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
