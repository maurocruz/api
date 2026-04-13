<?php
namespace Plinct\Api\Application\Entities;

use Plinct\Api\Application\Entities\Thing\ThingUpdateUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

readonly class EntityUpdateUseCase extends ThingUpdateUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function update(string $type, array $params): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams($type, $params);

		// SE FOR PARA ATUALIZAR RELACIONAMENTO
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		if ($idIsPartOf && $idHasPart) {
			$caption = $sanitizedParams['caption'] ?? null;
			$position = isset($sanitizedParams['position']) ? (int) $sanitizedParams['position'] : 0;
			$representativeOfPage = $sanitizedParams['representativeOfPage'] ?? '';
			return self::updateRelationship($idHasPart, $idIsPartOf, $caption, $position, $representativeOfPage);
		}

		// VERIFICAR INPUTS
		if (!$type || !$sanitizedParams) {
			return ['status' => false, 'message'=>'Type or params is required', 'data'=> []];
		}
		// VERIFICAR A EXISTÊNCIA DE ID
		$idthing = $sanitizedParams['idthing'] ?? $sanitizedParams['thing'] ?? null;
		if (!$idthing) {
			return ['status' => false, 'message'=>'Idthing is required', 'data'=> []];
		}
		// VERIFICAR SE O ITEM EXISTE
		$entity = SupportApplication::getEntity(ucfirst($type));
		if (!$entity) {
			return ['status' => false, 'message'=>'Module not found', 'data'=> []];
		}

		// ATUALIZAR DEPENDÊNCIAS, SE HOUVER
		$moduleName = ucfirst($type);
		$results = $this->updateDependencies($moduleName, $sanitizedParams, $idthing);
		if ($results['status'] === false) {
			return $results;
		}

		// ATUALIZAR ITEM
		$payload = SupportApplication::hydrateOwnProperties($moduleName, $sanitizedParams);
		if (!empty($payload)) {
			$results = $this->databaseActions->update(lcfirst($type), $payload, ['thing' => $idthing]);
			if ($results['status'] === false) {
				return $results;
			}
		}

		// PEGA ITEM ATUALIZADO, HIDRATA E RETORNA
		$results = $this->databaseActions->read(lcfirst($type), ['thing' => $idthing])->render();
		if ($results['status'] === false) {
			return $results;
		}
		$hydratedItem = SupportApplication::hydrateFromDatabase($results['data']);
		$result = SupportApplication::convertFromObjectToSchemaOrgArrayDTO($hydratedItem);
		return ['status' => true, 'message' => 'Module updated successfully', 'data' => $result];
	}

	/**
	 * @throws ReflectionException
	 */
	private function updateDependencies(string $moduleName, array $params, int $idthing): array
	{
		$entity = SupportApplication::getEntity($moduleName);
		$returnsOk = [];
		foreach ($entity::DEPENDENCIES as $dependencyName) {
			$whereClause = $dependencyName == 'Thing' ? ['idthing' => $idthing] : ['thing' => $idthing];
			// HIDRATA E FILTRA PARÂMETROS
			$payload = SupportApplication::hydrateOwnProperties($dependencyName, $params);
			if (!empty($payload)) {
				$responseData = $this->databaseActions->update(lcfirst($dependencyName), $payload, $whereClause);
				if ($responseData['status'] === false) {
					return $responseData;
				}
				$returnsOk[] = $responseData;
			}
		}
		return ['status' => true, 'message' => 'Dependencies are update', 'data' => $returnsOk];
	}
}
