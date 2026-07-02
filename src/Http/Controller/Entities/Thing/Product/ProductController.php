<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Product;

use Plinct\Api\Application\Entities\Thing\Product\ProductReadUseCase;
use Plinct\Api\Application\Entities\Thing\Product\ProductCreateUseCase;
use Plinct\Api\Support\SupportController;
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
		$sanitizedParams = SupportController::sanitizeParams('Product', $queryParams);
		$responseData = SupportController::returnDTO($this->productReadUseCase->read($sanitizedParams));
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
		$sanitizedParams = SupportController::sanitizeParams('Product', $params);
		$resultCreate = SupportController::returnDTO($this->productCreateUseCase->create($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($resultCreate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
