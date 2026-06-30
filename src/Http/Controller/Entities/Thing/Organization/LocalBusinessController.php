<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Organization;

use Plinct\Api\Application\Entities\Thing\Organization\LocalBusinessUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class LocalBusinessController
{
	public function __construct(
		private LocalBusinessUseCase $localBusinessUseCase
	) { }

	/**
	 * @throws \ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('LocalBusiness', $queryParams);
		$dataUseCase = $this->localBusinessUseCase->read($sanitizedParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
