<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Product;

use Plinct\Api\Application\Entities\Thing\Product\ProductReadUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class ProductReadController
{
	public function __construct(private ProductReadUseCase $productUseCase)
	{}

	/**
	 * @throws ReflectionException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$responseArray = $this->productUseCase->read($queryParams);
		if ($responseArray['status'] === true ) {
			$responseArray =  $responseArray['data'];
		}
		$response->getBody()->write(json_encode($responseArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
