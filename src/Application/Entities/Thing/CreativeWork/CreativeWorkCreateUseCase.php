<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;

class CreativeWorkCreateUseCase extends ThingCreateUseCase
{
	public function create(array $parseBody): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('creativeWork', $parseBody);

		// VERIFICA SE NÃO É APENAS RELACIONAMENTO
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		if ($idHasPart && $idIsPartOf) {
			return $this->createRelationship($idHasPart, $idIsPartOf);
		}

		if (!isset($sanitizedParams['name'])) {
			return ['status' => false, 'message' => 'Name is required'];
		}
		// SAVE THING
		$saveThing = parent::create($sanitizedParams);
		if($saveThing['status'] === false) {
			return $saveThing;
		} else {
			$thingData = $saveThing['data'][0];
			$thing = $thingData['idthing'];

			$hidratedParams = SupportApplication::hydrateOwnProperties('creativeWork', $sanitizedParams);
			$saveCreativework = $this->databaseActions->create('creativeWork', $hidratedParams + ['thing' => $thing]);

			if ($saveCreativework['status'] === false) {
				return $saveCreativework;
			} else {
				$creativeWorkData = $saveCreativework['data'];
				return ['status' => true, 'message' => 'CreativeWork item has created', 'data' => $creativeWorkData];
			}
		}
	}
}
