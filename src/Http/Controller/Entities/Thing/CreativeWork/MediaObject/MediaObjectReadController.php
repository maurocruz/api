<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\MediaObjectReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class MediaObjectReadController
{
  public function __construct(
		private MediaObjectReadUseCase $mediaObjectUseCase
  ) { }

	/**
	 * @throws ReflectionException
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('MediaObject', $queryParams);
		$responseData = SupportController::returnDTO($this->mediaObjectUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
		return $response;

	}
}
