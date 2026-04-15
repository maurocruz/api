<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\PropertyValue;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;

class PropertyValueCreateUseCase extends ThingCreateUseCase
{
	public function create(array $parseBody = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('propertyValue', $parseBody);
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$name = $sanitizedParams['name'] ?? null;
		$value = $sanitizedParams['value'] ?? null;

		// VALIDAR ENTRADAS
		if (!$idHasPart || !$name || !$value) {
			return ['status' => false, 'message' => 'Missing required fields', 'data' => []];
		}

		// SALVAR THING E SALVAR RELACIONAMENTO
		$sanitizedParams['type'] = 'propertyValue';
		$dadosCreateThing = parent::create($sanitizedParams);
		if ($dadosCreateThing['status'] === false) {
			return $dadosCreateThing;
		}
		$thingId = $dadosCreateThing['data'][0]['idthing'];

		// SALVAR PROPERTY VALUE
		$dataCreatePropertyValue = $this->databaseActions->create('propertyValue', ['thing' => $thingId] + $sanitizedParams);
		if ($dataCreatePropertyValue['status'] === false) {
			return $dataCreatePropertyValue;
		}
		// RETORNAR
		return SupportApplication::returnDTOFromDatabaseData($dataCreatePropertyValue);
	}
}
