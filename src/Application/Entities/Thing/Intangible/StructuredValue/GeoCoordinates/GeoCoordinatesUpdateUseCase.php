<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\GeoCoordinates;

use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class GeoCoordinatesUpdateUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	)	{ }

	/**
	 * @throws ReflectionException
	 */
	public function update(array $sanitizedParams): array
	{
		// VALIDAR ENTRADAS
		$idgeoCoordinates = $sanitizedParams['idgeoCoordinates'] ?? null;
		if (!$idgeoCoordinates) {
			return ['status' => false, 'message' => 'Missing required fields'];
		}

		// ATUALIZAR GEO COORDINATES
		$databaseData = $this->databaseActions->update('geoCoordinates', $sanitizedParams, ['idgeoCoordinates' => $idgeoCoordinates]);
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		// OBTER O ITEM ATUALIZADO
		$getDataItem = $this->databaseActions->read('geoCoordinates', ['idgeoCoordinates' => $idgeoCoordinates]);
		$getDataItem->setJoin('postalAddress', 'postalAddress.idpostalAddress = geoCoordinates.address');
		$dataItem = $getDataItem->render();
		if ($dataItem['status'] === false) {
			return $dataItem;
		}
		foreach ($dataItem['data'] as $key => $value) {
			$dataItem['data'][$key]['type'] = 'GeoCoordinates';
		}
		return ['status' => true, 'message' => 'Update geoCoordinates sucessfully', 'data'=>$dataItem['data'] ];
	}
}
