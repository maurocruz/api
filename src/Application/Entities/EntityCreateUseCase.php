<?php
namespace Plinct\Api\Application\Entities;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;
use ReflectionException;

class EntityCreateUseCase extends ThingCreateUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function createEntity(string $type, array $sanitizedParams): array
	{
		// VERIFICA SE NÃO É APENAS RELACIONAMENTO
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		if ($idHasPart && $idIsPartOf) {
			return $this->createRelationship($idHasPart, $idIsPartOf);
		}
		// SAVE THING
		$saveThing = parent::create($sanitizedParams);
		if($saveThing['status'] === false) {
			return $saveThing;
		} else {
			$thingData = $saveThing['data'][0];
			$thing = $thingData['idthing'];

			$saveCreativework = $this->databaseActions->create($type, $sanitizedParams + ['thing' => $thing]);

			if ($saveCreativework['status'] === false) {
				return $saveCreativework;
			} else {
				$creativeWorkData = $saveCreativework['data'];
				return ['status' => true, 'message' => "$type item has created", 'data' => $creativeWorkData];
			}
		}
	}
}
