<?php
namespace Plinct\Api\Http\Controller;

use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class HomeController
{

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$useCaseData = ConfigurationDomain::verbose();
		$responseJson = json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		$response->getBody()->write($responseJson);
		return $response;
	}
}
