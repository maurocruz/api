<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\GeoCoordinates;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class GeoCoordinatesCreateUseCase
{

	public function __construct(
		private DatabaseActions $databaseActions
	) {	}

	/**
	 * @throws ReflectionException
	 */
	public function create(array $sanitizedParams): array
	{
		$latitude = $sanitizedParams['latitude'] ?? null;
		$longitude = $sanitizedParams['longitude'] ?? null;
		$elevation = $sanitizedParams['elevation'] ?? null;
		$streetAddress = $sanitizedParams['streetAddress'] ?? null;
		$addressLocality = $sanitizedParams['addressLocality'] ?? null;
		$addressRegion = $sanitizedParams['addressRegion'] ?? null;
		$addressCountry = $sanitizedParams['addressCountry'] ?? null;
		$postalCode = $sanitizedParams['postalCode'] ?? null;
		$address = null;

		// VERIFICA ENTRADAS OBRIGATÓRIAS
		if (!$latitude || !$longitude) {
			return ['status' => false, 'message' => 'Please enter latitude and longitude.', 'data' => null];
		}

		// VERIFICA SE ENTROU POSTAL ADDRESS, SE SIM, SALVA ADDRESS
		if ($streetAddress || $addressLocality || $addressRegion || $addressCountry || $postalCode) {
			$dataCreatePostalAddress = $this->databaseActions->create('postalAddress', [
				'streetAddress' => $streetAddress,
				'addressLocality' => $addressLocality,
				'addressRegion' => $addressRegion,
				'addressCountry' => $addressCountry,
				'postalCode' => $postalCode
			]);
			if ($dataCreatePostalAddress['status'] === false) {
				return $dataCreatePostalAddress;
			}
			$data = $dataCreatePostalAddress['data'][0];
			$address = $data['idpostalAddress'] ?? null;
		}
		// SALVA GEO COORDINATES
		$dataCreate = $this->databaseActions->create('geoCoordinates', ['latitude' => $latitude, 'longitude' => $longitude, 'elevation' => $elevation, 'address' => $address]);
		if ($dataCreate['status'] === false) {
			return $dataCreate;
		}

		// INSERE TIPO
		foreach ($dataCreate['data'] as $key => $value) {
			$dataCreate['data'][$key]['type'] = 'GeoCoordinates';
		}
		return $dataCreate;
	}
}
