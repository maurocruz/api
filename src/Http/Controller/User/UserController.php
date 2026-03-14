<?php
namespace Plinct\Api\Http\Controller\User;

use Plinct\Api\Application\User\UserDeleteUseCase;
use Plinct\Api\Application\User\UserReadUseCase;
use Plinct\Api\Application\User\UserUpdateUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class UserController
{
	public function __construct(
		private UserReadUseCase $userReadUseCase,
		private UserUpdateUseCase $userUpdateUseCase,
		private UserDeleteUseCase $userDeleteUseCase
	){	}

	// READ
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getQueryParams();
		$dataUser = $this->userReadUseCase->read($params);
		$response->getBody()->write(json_encode($dataUser, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	// UPDATE
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$dataUpdate = $this->userUpdateUseCase->update($params);
		$response->getBody()->write(json_encode($dataUpdate, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	// DELETE
	public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$dataDelete = $this->userDeleteUseCase->delete($queryParams);
		$response->getBody()->write(json_encode($dataDelete, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
