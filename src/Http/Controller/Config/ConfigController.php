<?php
namespace Plinct\Api\Http\Controller\Config;

use Exception;
use Plinct\Api\Application\Config\InstallModuleUseCase;
use Plinct\Api\Application\Config\UpdateVersionUseCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class ConfigController
{
	public function __construct(
		private InstallModuleUseCase $installModuleUseCase,
		private UpdateVersionUseCase $updateVersionUseCase
	) { }

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
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

	/**
	 * @throws Exception
	 */
	public function update(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$queryParams = $request->getQueryParams();
		if ($queryParams['update'] === 'v2tov3') {
			$returnData = $this->updateVersionUseCase->v2topv3();
		} else {
			$returnData = ['status'=>false, 'message' => 'No module name provided', 'data' =>[]];
		}
		$response->getBody()->write(json_encode($returnData));
		return $response;
	}
}
