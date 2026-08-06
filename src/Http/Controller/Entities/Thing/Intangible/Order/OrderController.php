<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\Order;

use Plinct\Api\Application\Entities\Thing\Intangible\Order\OrderReadUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\Order\OrderUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class OrderController
{
	public function __construct(
		private OrderReadUseCase $orderReadUseCase,
		private OrderUseCase $orderUseCase
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('Order', $queryParams);
		$dataUseCase = $this->orderReadUseCase->read($sanitizedParams);
		$dto = SupportController::returnDTO($dataUseCase);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parseBody = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams('Order', $parseBody);
		$dataUseCase = $this->orderUseCase->create($sanitizedParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('Order', $request->getParsedBody());
		$useCaseData = $this->orderUseCase->update($sanitizedParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 * @throws ReflectionException
	 */
	public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('Order', $request->getQueryParams());
		$useCaseData = $this->orderUseCase->delete($sanitizedParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
