<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject;

use Exception;
use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\MediaObjectCreateUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class MediaObjectCreateController
{
	public function __construct(
		private MediaObjectCreateUseCase $mediaObjectCreateUseCase
	) { }

	/**
	 * @throws Exception
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$fileupload = $request->getUploadedFiles();
		$sanitizedParams = SupportController::sanitizeParams('MediaObject', $params);
		$dataUseCase = SupportController::returnDTO($this->mediaObjectCreateUseCase->create($sanitizedParams, $fileupload));
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
		return $response;
	}

}
