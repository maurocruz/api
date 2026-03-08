<?php
namespace Plinct\Api\Http\Controller\Modules;

use Plinct\Api\Application\Modules\ModuleDeleteUseCase;
use Plinct\Api\Application\Modules\ModuleUpdateUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ModuleActionsController
{
	public function __construct(private ModuleDeleteUseCase $moduleDeleteUseCase, private ModuleUpdateUseCase $moduleUpdateUseCase)
	{
	}

	// UPDATE
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type') ?? null;
		$params = $request->getParsedBody() ?? [];
		$responseData = $this->moduleUpdateUseCase->update($type, $params);
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}

	// DELETE
	public function delete(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$type = $request->getAttribute('type');
		$id = $request->getQueryParams()['idthing'] ?? $request->getQueryParams()['thing'] ?? $request->getQueryParams()['id'] ?? $request->getQueryParams()["id$type"] ?? null;
		$responseData = $this->moduleDeleteUseCase->delete($type, $id);
		$response->getBody()->write(json_encode($responseData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	}
}
