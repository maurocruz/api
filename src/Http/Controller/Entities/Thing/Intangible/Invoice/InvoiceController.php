<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\Invoice;

use Plinct\Api\Application\Entities\Thing\Intangible\Invoice\InvoiceReadUseCase;
use Plinct\Api\Application\Entities\Thing\Intangible\Invoice\InvoiceUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class InvoiceController
{
	public function __construct(
		private InvoiceUseCase $invoiceUseCase,
		private InvoiceReadUseCase $invoiceReadUseCase,
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('invoice', $queryParams);
		$dataUseCase = $this->invoiceReadUseCase->read($sanitizedParams);
		$returnDTO = SupportController::returnDTO($dataUseCase);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($returnDTO, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('Invoice', $request->getParsedBody());
		$useCaseData = $this->invoiceUseCase->create($sanitizedParams);
		$dto = SupportController::returnDTO($useCaseData);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('Invoice', $request->getParsedBody());
		$useCaseData = $this->invoiceUseCase->update($sanitizedParams);
		$dto = SupportController::returnDTO($useCaseData);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('Invoice', $request->getQueryParams());
		$useCaseData = $this->invoiceUseCase->delete($sanitizedParams);
		$dto = SupportController::returnDTO($useCaseData);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
