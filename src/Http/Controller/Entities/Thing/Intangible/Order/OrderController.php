<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\Order;

use Plinct\Api\Application\Entities\Thing\Intangible\Order\OrderReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class OrderController
{
	public function __construct(
		private OrderReadUseCase $orderReadUseCase,
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
}
