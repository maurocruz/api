<?php
namespace Plinct\Api\Http\Controller\Config;

use Plinct\Api\Application\Config\InstallModuleUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ConfigController
{
	public function __construct(private InstallModuleUseCase $installModuleUseCase)
	{
	}
	public function installModule(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$moduleName = $request->getParsedBody()['moduleName'] ?? null;
		if ($moduleName) {
			$returnData = $this->installModuleUseCase->installModule($moduleName);
		} else {
			$returnData = ['status'=>false, 'message' => 'No module name provided', 'data' =>[]];
		}
		$response->getBody()->write(json_encode($returnData));
		return $response;
	}
}
