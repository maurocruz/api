<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Organization;

use Plinct\Api\Application\Entities\Thing\Organization\OrganizationUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class OrganizationController
{
	public function __construct(
		private OrganizationUseCase $organizationUseCase
	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('Organization', $queryParams);
		$dataUseCase = SupportController::returnDTO($this->organizationUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
