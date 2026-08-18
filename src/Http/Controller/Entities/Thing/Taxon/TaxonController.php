<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Taxon;

use Plinct\Api\Application\Entities\Thing\Taxon\TaxonReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class TaxonController
{
	public function __construct(
		private TaxonReadUseCase $taxonReadUseCase
	) {}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('Taxon', $queryParams);
		$dataUseCase = SupportController::returnDTO($this->taxonReadUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		return $response;
	}
}
