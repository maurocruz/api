<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\ImageObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\ImageObject\ImageObjectCreateUseCase;
use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\ImageObject\ImageObjectReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class ImageObjectController
{
	public function __construct(
		private ImageObjectReadUseCase $imageObjectReadUseCase,
		private ImageObjectCreateUseCase $imageObjectCreateUseCase,
	)	{	}

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('ImageObject', $queryParams);
		$responseData = SupportController::returnDTO($this->imageObjectReadUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	/**
	 * @throws ReflectionException
	 */
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parseBody = $request->getParsedBody();
		$sanitizedParams = SupportController::sanitizeParams('ImageObject', $parseBody);
		$responseData = SupportController::returnDTO($this->imageObjectCreateUseCase->create($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
