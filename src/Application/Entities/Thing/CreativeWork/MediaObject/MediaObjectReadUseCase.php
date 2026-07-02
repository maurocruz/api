<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use ReflectionException;

class MediaObjectReadUseCase extends CreativeWorkReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		// IF ID HAS PART
		$idHasPart = $params['idHasPart'] ?? null;
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', $sanitizedParams);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idHasPart');
		} else {
			$getData = $this->databaseActions->read('mediaObject', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.idcreativeWork=mediaObject.creativeWork');
		}
		$databaseData = $getData->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		$data = $databaseData['data'];

		// PROPERTIES
		$properties = $sanitizedParams['properties'] ?? [];
		if (!empty($properties)) {
			foreach ($data as $key => $value) {
				$idthing = $value['thing'] ?? $value['idthing'] ?? null;
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$dataGet = $this->databaseActions->read('thing_has_thing', ['idIsPartOf' => $idthing] + $sanitizedParams);
					$dataGet->setJoin('thing', 'thing.idthing = thing_has_thing.idHasPart');
					$dataGet->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idHasPart');
					$dataIsPartOf = $dataGet->render();
					if ($dataIsPartOf['status'] === false) {
						return $dataIsPartOf;
					}
					$databaseData['data'][$key]['isPartOf'] = $dataIsPartOf['data'] ?? [];
				}
			}
		}
		return $databaseData;
	}

}
