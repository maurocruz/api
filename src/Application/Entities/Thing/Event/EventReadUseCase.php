<?php
namespace Plinct\Api\Application\Entities\Thing\Event;

use Plinct\Api\Application\Entities\Thing\Place\PlaceReadUseCase;
use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

class EventReadUseCase extends ThingReadUseCase
{
	public function __construct(
		DatabaseActions $databaseActions,
		private readonly PlaceReadUseCase $placeReadUseCase
	) {
		parent::__construct($databaseActions);
	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		// OBTER DADOS DO BANCO DE DADOS
		$getData = $this->databaseActions->read('event', $sanitizedParams);
		$data = $getData->render();
		if ($data['status'] === false) {
			return $data;
		}

		// PROPERTIES
		$properties = $sanitizedParams['properties'] ?? null;
		if ($properties) {
			foreach ($data['data'] as $key => $value) {
				$location = $value['location'] ?? null;
				$superEvent = $value['superEvent'] ?? null;
				// LOCATION
				if (in_array('location', $properties) && $location) {
					$dataLocation = $this->placeReadUseCase->read(['thing' => $location, 'properties' => 'geo']);
					if ($dataLocation['status'] === false) {
						return $dataLocation;
					}
					$data['data'][$key]['location'] = $dataLocation['data'][0];
				}

				// SUPER EVENT
				// TODO precisa mudar de idevent para idthing
				if ($superEvent && in_array('superEvent', $properties)) {
					$dataSuperEvent = $this->databaseActions->read('event', ['idevent'=>$superEvent])->render();
					if ($dataSuperEvent['status'] === false) {
						return $dataSuperEvent;
					}
					$data['data'][$key]['superEvent'] = $dataSuperEvent['data'][0] ?? null;
				}

				// SUB EVENT
				// TODO precisa mudar de idevent para idthing
				if (in_array('subEvent', $properties)) {
					$dataSubEvent = $this->read(['superEvent'=>$value['idevent'],'properties'=>'location','fields'=>'location,name,startDate']);
					if ($dataSubEvent['status'] === false) {
						return $dataSubEvent;
					}
					$data['data'][$key]['subEvent'] = $dataSubEvent['data'] ?? null;
				}
			}

		}
		return $data;
	}
}
