<?php
namespace Plinct\Api\Http\Controller\User;

use Plinct\Api\Application\User\UserReadUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class UserController
{
	public function __construct(private UserReadUseCase $userReadUseCase)
	{

	}

	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getQueryParams();
		$dataUser = $this->userReadUseCase->read($params);
		$response->getBody()->write(json_encode($dataUser, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

}
