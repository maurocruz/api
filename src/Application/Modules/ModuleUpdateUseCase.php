<?php
namespace Plinct\Api\Application\Modules;

use Plinct\Api\Domain\Modules\ModuleDomain;
use Plinct\Api\Domain\Support\DataHandler;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class ModuleUpdateUseCase
{
	public function __construct(private DatabaseActions $databaseActions)
	{}

	/**
	 * @throws ReflectionException
	 */
	public function update(string $type = null, array $params = []): array
	{
		$idthing = $params['idthing'] ?? $params['thing'] ?? null;
		// VERIFICAR INPUTS
		if (!$type || !$params) {
			return ['status' => false, 'message'=>'Type or params is required', 'data'=> []];
		}
		// VERIFICAR A EXISTÊNCIA DE ID
		if (!$idthing) {
			return ['status' => false, 'message'=>'Idthing is required', 'data'=> []];
		}
		// VERIFICAR SE O ITEM EXISTE
		if (!ModuleDomain::moduleExists(ucfirst($type))) {
			return ['status' => false, 'message'=>'Module not found', 'data'=> []];
		}

		$moduleName = ucfirst($type);
		// ATUALIZAR DEPENDÊNCIAS, SE HOUVER
		$results = $this->updateDependencies($moduleName, $params, $idthing);

		// ATUALIZAR ITEM
		$payload = DataHandler::payloadBuilder(ModuleDomain::getModuleObject($moduleName), $params);
		if (!empty($payload)) {
			$resultItem = $this->databaseActions->update($moduleName, $payload, ['thing' => $idthing]);
			if ($resultItem['status'] === false) {
				return $resultItem;
			} else {
				$results[] = $resultItem;
			}
		}
		return ['status' => true, 'message' => 'Module updated successfully', 'data' => $results];
	}

	/**
	 * @throws ReflectionException
	 */
	private function updateDependencies(string $moduleName, array $params, int $idthing): array
	{
		$results = [];
		foreach (ModuleDomain::getDependenciesOfModule($moduleName) as $dependencyName) {
			$whereClause = $dependencyName == 'Thing' ? ['idthing' => $idthing] : ['thing' => $idthing];
			// HIDRATA E FILTRA PARÂMETROS
			$payload = DataHandler::payloadBuilder(ModuleDomain::getModuleObject($dependencyName), $params);
			if (!empty($payload)) {
				$resultDep = $this->databaseActions->update($dependencyName, $payload, $whereClause);
				if ($resultDep['status'] === false) {
					return $resultDep;
				} else {
					$results[] = $resultDep;
				}
			}
		}
		return $results;
	}
}
