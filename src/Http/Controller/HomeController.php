<?php
namespace Plinct\Api\Http\Controller;

use Plinct\Api\Application\Config\HomeUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class HomeController
{
	public function __construct(private HomeUseCase $homeUsecase)
	{
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$useCaseData = $this->homeUsecase->ready();
		$responseJson = json_encode($useCaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		$response->getBody()->write($responseJson);
		return $response;
	}

	/**
	 * @return AuthenticatorController
	 */
	public static function Auth(): AuthenticatorController
	{
		return new AuthenticatorController();
	}
}
