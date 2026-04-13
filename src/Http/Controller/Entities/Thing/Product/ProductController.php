<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Product;

use Plinct\Api\Application\Entities\Thing\Product\ProductReadUseCase;
use Plinct\Api\Application\Entities\Thing\Product\ProductCreateUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class ProductController
{
	public function __construct(
		private ProductReadUseCase $productReadUseCase,
		private ProductCreateUseCase $productCreateUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$responseData = $this->productReadUseCase->read($queryParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$resultCreate = $this->productCreateUseCase->create($params);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($resultCreate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
