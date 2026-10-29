<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Intangible\Role;

use Plinct\Api\Application\Entities\Thing\Intangible\Role\RoleUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class RoleController
{
	public function __construct(
		private RoleUseCase $roleUseCase
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('role', $request->getQueryParams());
		$useCaseData = SupportController::returnDTO($this->roleUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;

	}
}
