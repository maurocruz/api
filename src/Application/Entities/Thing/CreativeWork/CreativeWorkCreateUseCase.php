<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;

class CreativeWorkCreateUseCase extends ThingCreateUseCase
{
	public function create(array $sanitizedParams): array
	{
		// VERIFICA SE NÃO É APENAS RELACIONAMENTO
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		if ($idHasPart && $idIsPartOf) {
			return $this->createRelationship($idHasPart, $idIsPartOf);
		}
		if (!isset($sanitizedParams['name'])) {
			return ['status' => false, 'message' => 'Name is required'];
		}
		$sanitizedParams['type'] = $sanitizedParams['type'] ?? 'CreativeWork';
		// SAVE THING
		$saveThing = parent::create($sanitizedParams);
		if($saveThing['status'] === false) {
			return $saveThing;
		}
		$thingData = $saveThing['data'][0];
		$thing = $thingData['idthing'];
		return $this->databaseActions->create('creativeWork', $sanitizedParams + ['thing' => $thing]);
	}
}
