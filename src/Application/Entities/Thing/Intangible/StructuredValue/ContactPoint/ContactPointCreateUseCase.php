<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;

class ContactPointCreateUseCase extends ThingCreateUseCase
{
	public function create(array $sanitizedParams): array
	{
		// VALIDAR ENTRADAS
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$name = $sanitizedParams['name'] ?? null;
		$telephone = $sanitizedParams['telephone'] ?? null;
		$email = $sanitizedParams['email'] ?? null;

		if (!$idHasPart || !$name || (!$telephone || !$email)) {
			return ['status' => false, 'message' => 'Missing required fields', 'data' => []];
		}

		// SALVAR THING E SALVAR RELACIONAMENTO
		$sanitizedParams['type'] = 'contactPoint';
		$dadosCreateThing = parent::create($sanitizedParams);
		if ($dadosCreateThing['status'] === false) {
			return $dadosCreateThing;
		}
		$thingId = $dadosCreateThing['data'][0]['idthing'];

		// SALVAR PROPERTY VALUE
		return $this->databaseActions->create('contactPoint', ['thing' => $thingId] + $sanitizedParams);
	}
}
