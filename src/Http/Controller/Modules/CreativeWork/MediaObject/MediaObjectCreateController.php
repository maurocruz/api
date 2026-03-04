<?php
namespace Plinct\Api\Http\Controller\Modules\CreativeWork\MediaObject;

use Exception;
use Plinct\Api\Application\Modules\Thing\CreativeWork\MediaObject\MediaObjectCreateUseCase;
use Plinct\Api\Http\Response\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class MediaObjectCreateController
{
	public function __construct(private MediaObjectCreateUseCase $mediaObjectCreateUseCase)
	{
	}

	/**
	 * @throws Exception
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$fileupload = $request->getUploadedFiles();
		$dataUseCase = $this->mediaObjectCreateUseCase->create($params, $fileupload);

		if ($dataUseCase['status'] === true) {
			$dataUseCase['data'] = [ResponseFactory::parseSchema($dataUseCase['data'])];
		}

		$response->getBody()->write(json_encode($dataUseCase, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
		return $response;
	}

}
