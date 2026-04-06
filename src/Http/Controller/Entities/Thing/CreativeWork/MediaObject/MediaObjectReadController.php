<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject;

use Plinct\Api\Application\Modules\Thing\CreativeWork\MediaObject\MediaObjectReadUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class MediaObjectReadController
{
  public function __construct(private MediaObjectReadUseCase $mediaObjectUseCase) {

  }

	public  function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$responseArray = $this->mediaObjectUseCase->read($queryParams);
		if ($responseArray['status'] === true ) {
			$responseData =  $responseArray['data'];
		} else {
			$responseData = $responseArray;
		}
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
		return $response;

	}
}
