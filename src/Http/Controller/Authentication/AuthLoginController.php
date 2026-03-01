<?php
namespace Plinct\Api\Http\Controller\Authentication;

use Plinct\Api\ApiFactory;
use Plinct\Api\Application\Auth\AuthLoginUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthLoginController
{
	public function __construct(private AuthLoginUseCase $authLoginUseCase)
	{
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
	{
		$params = $request->getParsedBody();
		$data = $this->authLoginUseCase->login($params);
		return ApiFactory::response()->write($response, $data);
	}

}
