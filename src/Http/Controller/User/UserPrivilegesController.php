<?php
namespace Plinct\Api\Http\Controller\User;

use Plinct\Api\Application\User\UserPrivilegesUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class UserPrivilegesController
{
	public function __construct(private UserPrivilegesUseCase $userPrivilegesUseCase)
	{
	}

	// READ
	public function read(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		$databaseData = $this->userPrivilegesUseCase->read($queryParams);
		$response->getBody()->write(json_encode($databaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	// CREATE
	public function create(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getParsedBody();
		$databaseData = $this->userPrivilegesUseCase->create($params);
		$response->getBody()->write(json_encode($databaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	// DELETE
	public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$params = $request->getQueryParams();
		$databaseData = $this->userPrivilegesUseCase->delete($params);
		$response->getBody()->write(json_encode($databaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	// UPDATE
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$parseBody = $request->getParsedBody();
		$databaseData = $this->userPrivilegesUseCase->update($parseBody);
		$response->getBody()->write(json_encode($databaseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
