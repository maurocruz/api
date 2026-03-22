<?php
namespace Plinct\Api\Application\Entities\Thing;

use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Domain\Entities\Entities;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

class ThingReadUseCase
{
	public function __construct(
		protected readonly DatabaseActions $databaseActions
	) { }

	/**
	 * @throws ReflectionException
	 */
	public function readEntity(string $type, array $queryParams = []): array
	{
		// VERIFICA SE EXISTE ENTIDADE PARA TYPE
		if (!Entities::hasEntity($type)) {
			return ['status' => false, 'message' => 'Entity not found', 'data' => []];
		}
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams($type, $queryParams);
		// BUSCA POR IS PART OF (RELATIONSHIP)
		$isPartOf = $sanitizedParams['isPartOf'] ?? null;
		if ($isPartOf) {
			return self::readRelations($isPartOf, $type, $sanitizedParams);
		}
		// BUSCA POR PARAMS
		$responseData = $this->databaseActions->read(lcfirst($type), $sanitizedParams)->render();
		if ($responseData['status'] === false) {
			return $responseData;
		}
		return SupportApplication::returnDTOFromDatabaseData($responseData,  $type.' found');
	}

	/**
	 * @throws ReflectionException
	 */
	private function readRelations(string $idHasPart, string $typeHasPart, array $addtionalparams = []): array
	{
		$addtionalparams = ['orderBy'=>'position', 'ordering'=>'desc'] + $addtionalparams;
		$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf' => ucfirst($typeHasPart)] + $addtionalparams);
		$responseData = $getData->render();
		if ($responseData['status'] === false) {
			return $responseData;
		}
		$data = $responseData['data'];
		if (empty($data)) {
			return ['status'=> true, 'message'=> $typeHasPart . ' not found', 'data'=>[]];
		}
		$things = [];
		foreach ($data as $value) {
			$things[] = $value['idIsPartOf'];
		}
		$typehasPartData = $this->databaseActions->read(lcfirst($typeHasPart), ['thing' => implode('|',$things)])->render();
		if ($typehasPartData['status'] === false) {
			return $typehasPartData;
		}
		$entities = SupportApplication::hydrateFromDatabase($typehasPartData['data']);
		return ['status'=> true, 'message'=> $typeHasPart . ' found', 'data'=> SupportApplication::convertFromObjectToSchemaOrgArrayDTO($entities)];
	}
}
