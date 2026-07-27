<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\VideoObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\VideoObject\VideoObjectReadUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionException;

readonly class VideoObjectController
{
	public function __construct(
		private VideoObjectReadUseCase $videoObjectReadUseCase
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$sanitizedParams = SupportController::sanitizeParams('ImageObject', $queryParams);
		$responseData = SupportController::returnDTO($this->videoObjectReadUseCase->read($sanitizedParams));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
