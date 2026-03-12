<?php
namespace Plinct\Api\Http\Controller\Authentication;

use Plinct\Api\ApiFactory;
use Plinct\Api\Application\User\UserLoginUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class AuthLoginController
{
	public function __construct(private UserLoginUseCase $authLoginUseCase)
	{
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$data = $this->authLoginUseCase->login($params);
		return ApiFactory::response()->write($response, $data);
	}

}
