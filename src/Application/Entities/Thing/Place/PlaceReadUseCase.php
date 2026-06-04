<?php
namespace Plinct\Api\Application\Entities\Thing\Place;

use Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\GeoCoordinates\GeoCoordinatesReadUseCase;
use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

class PlaceReadUseCase extends ThingReadUseCase
{
	public function __construct(
		DatabaseActions $databaseActions,
		private readonly GeoCoordinatesReadUseCase $geoCoordinatesReadUseCase
	) {
		parent::__construct($databaseActions);
	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		// SANITIZE PARAMS
		$queryParams = SupportApplication::sanitizeParams('place', $queryParams);

		// SE HOUVER IDHASPART
		$idHasPart = $queryParams['idHasPart'] ?? null;
		if ($idHasPart !== null) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf'=>'Place']);
			$getData->setJoin('thing','thing.idthing=thing_has_thing.idIsPartOf');
			$getData->setJoin('place','place.thing=thing_has_thing.idIsPartOf');
			$data = $getData->render();
			if ($data['status'] === false) {
				return $data;
			}
			return SupportApplication::returnDTOFromDatabaseData($data);
		}

		// OBTÉM DADOS DO BANCO DE DADOS
		$data = $this->readEntity('place', $queryParams);
		if ($data['status'] === false) {
			return $data;
		}

		// IF PROPERTIES
		$properties = $queryParams['properties'] ?? null;
		if (!empty($properties)) {
			foreach ($data['data'] as $key => $place) {
				// GEO
				if (in_array('geo', $properties)) {
					$geo = $place['geo'] ?? null;
					if (!$geo) continue;
					$dataGeo = $this->geoCoordinatesReadUseCase->read(['idgeoCoordinates' => $geo, 'properties' => 'address']);
					if ($dataGeo['status'] === false) {
						return $dataGeo;
					}
					$valueGeo = $dataGeo['data'][0];
					$valueGeo['type'] = 'GeoCoordinates';
					$data['data'][$key]['geo'] = $valueGeo;
				}
			}
		}
		return $data;
	}
}
