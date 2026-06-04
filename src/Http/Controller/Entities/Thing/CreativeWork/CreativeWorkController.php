<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class CreativeWorkController
{
	public function __construct(
		private CreativeWorkReadUseCase $creativeWorkReadUseCase
	)	{ }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$responseRead = $this->creativeWorkReadUseCase->read($queryParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode(SupportApplication::returnDTOFromDatabaseData($responseRead), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
