<?php
namespace Plinct\Api\Application\Entities\Thing\Event;

use Plinct\Api\Application\Entities\Thing\ThingCreateUseCase;
use Plinct\Api\Application\Support\SupportApplication;

class EventCreateUseCase extends ThingCreateUseCase
{
	public function create(array $sanitizedParams): array
	{
		// VALIDAR ENTRADAS
		$name = $sanitizedParams['name'] ?? null;
		$startDate = $sanitizedParams['startDate'] ?? null;
		$endDate = $sanitizedParams['endDate'] ?? null;
		$location = $sanitizedParams['location'] ?? null;
		if (!$name || !$startDate || !$endDate || !$location) {
			return SupportApplication::returnDTO(false, 'Missing required fields');
		}

		// THING
		$sanitizedParams['type'] = 'Event';
		$thingData = parent::create($sanitizedParams);
		if ($thingData['status'] === false) {
			return $thingData;
		}
		$valueThing = $thingData['data'][0];

		// SALVA EVENTO
		return $this->databaseActions->create('event', ['thing' => $valueThing['idthing']] + $sanitizedParams);
	}
}
