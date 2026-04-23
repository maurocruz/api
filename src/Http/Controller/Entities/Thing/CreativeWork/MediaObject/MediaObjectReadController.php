<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject;

use Plinct\Api\Application\Modules\Thing\CreativeWork\MediaObject\MediaObjectReadUseCase;
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
		$responseData = $this->mediaObjectUseCase->read($queryParams);
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
		return $response;

	}
}
