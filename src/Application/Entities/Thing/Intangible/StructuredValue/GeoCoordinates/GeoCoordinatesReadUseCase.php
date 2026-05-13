<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\GeoCoordinates;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class GeoCoordinatesReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('GeoCoordinates',$queryParams);

		// OBTÉM DADOS DO BANCO DE DADOS
		$getData = $this->databaseActions->read('geoCoordinates', $sanitizedParams)->setWithThings(false);
		$data = $getData->render();

		if ($data['status'] === false) {
			return $data;
		}
		// INSERE TIPO
		foreach ($data['data'] as $key => $value) {
			$data['data'][$key]['type'] = 'GeoCoordinates';
		}


		// IF PROPERTIES
		$properties = $sanitizedParams['properties'] ?? null;
		if ($properties) {
			foreach ($data['data'] as $key => $value) {
				// ADDRESS
				if (in_array('address', $properties)) {
					$address = $value['address'] ?? null;
					$getDataAddress = $this->databaseActions->read('postalAddress', ['idpostalAddress' => $address])->setWithThings(false);
					$dataAddress = $getDataAddress->render();
					if ($dataAddress['status'] === false) {
						return $dataAddress;
					}
					$valueAddress = $dataAddress['data'][0] ?? null;
					if (!$valueAddress) continue;
					$valueAddress['type'] = 'PostalAddress';
					$data['data'][$key]['address'] = $valueAddress;
				}
			}
		}
		return $data;
	}
}
