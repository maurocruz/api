<?php
namespace Plinct\Api\Http\Controller\Entities\Thing\Person;

use Exception;
use Plinct\Api\Application\Entities\Thing\Person\PersonUseCase;
use Plinct\Api\Support\SupportController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class PersonController
{
	 public function __construct(
		 private PersonUseCase $personUseCase
	 ) { }

	/**
	 * @throws Exception
	 */
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$sanitizedParams = SupportController::sanitizeParams('person',$request->getQueryParams());
		$useCaseData = $this->personUseCase->read($sanitizedParams);
		if ($useCaseData['status'] === false) {
			throw new Exception($useCaseData['message']);
		}
		$response = $response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode($useCaseData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		return $response;
	}
}
