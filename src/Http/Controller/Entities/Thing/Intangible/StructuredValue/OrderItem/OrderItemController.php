<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\OrderItem;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\OrderItem\OrderItemUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\OrderItem\OrderItemReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class OrderItemController
{
	public function __construct(
		private OrderItemReadUseCase   $orderItemReadUseCase,
		private OrderItemUseCase       $orderItemUseCase,
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('OrderItem', $queryParams);
		$useCaseData = $this->orderItemReadUseCase->read($sanitizedParams);
		$dto = SupportController::returnDTO($useCaseData);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams('OrderItem', $params);
		$useCaseData = $this->orderItemUseCase->create($sanitizedParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('OrderItem', $request->getParsedBody());
		$useCaseData = $this->orderItemUseCase->update($sanitizedParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('OrderItem', $request->getQueryParams());
		$useCaseData = $this->orderItemUseCase->delete($sanitizedParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
