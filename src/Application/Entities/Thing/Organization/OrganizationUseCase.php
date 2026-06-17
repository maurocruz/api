<?php
namespace Plinct\Api\Application\Entities\Thing\Organization;

use Plinct\Api\Application\Entities\Thing\Place\PlaceReadUseCase;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class OrganizationUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions,
		private PlaceReadUseCase $placeReadUseCase
	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$properties = $sanitizedParams['properties'] ?? [];

		// OBTÉM DADOS DO BANCO DE DADOS
		$getData = $this->databaseActions->read('organization', $sanitizedParams);
		$databaseData = $getData->render();

		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		foreach ($databaseData['data'] as $key => $value) {
			// LOCATION
			if (in_array('location', $properties)) {
				$paramsPlace = ['thing' => $value['location']];
				if (in_array('geo', $properties)) {
					$paramsPlace['properties'] = 'geo';
				}
				$placeData = $this->placeReadUseCase->read($paramsPlace);
				if ($placeData['status'] === false) {
					return $placeData;
				}
				$databaseData['data'][$key]['location'] = $placeData['data'][0];
			}
		}
		return $databaseData;
	}
}
