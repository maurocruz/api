<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\PropertyValue;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;

class PropertyValueCreateUseCase extends ThingCreateUseCase
{
	public function create(array $sanitizedParams): array
	{
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
		return $this->databaseActions->create('propertyValue', ['thing' => $thingId] + $sanitizedParams);
	}
}
