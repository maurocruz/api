<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\VideoObject;

use Plinct\Api\Application\Entities\Thing\CreativeWork\MediaObject\MediaObjectReadUseCase;

class VideoObjectReadUseCase extends MediaObjectReadUseCase
{
	public function read(array $sanitizedParams): array
	{
		$properties = $sanitizedParams['properties'] ?? [];
		$idHasPart = $sanitizedParams['idHasPart'] ?? $sanitizedParams['isPartOf'] ?? null;

		// OBTÉM DADOS DO BANCO DE DADOS
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart'=>$idHasPart, 'typeIsPartOf' => 'VideoObject'] + $sanitizedParams);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
			$getData->setJoin('mediaObject', 'mediaObject.thing = thing_has_thing.idIsPartOf');
			$getData->setJoin('videoObject', 'videoObject.thing = thing_has_thing.idIsPartOf');
		} else {
			$getData = $this->databaseActions->read('videoObject', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = videoObject.thing');
			$getData->setJoin('mediaObject', 'mediaObject.thing = videoObject.thing');
		}
		$databaseData = $getData->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}
		$data = $databaseData['data'];

		// PROPERTIES
		if (!empty($properties)) {
			foreach ($data as $key => $value) {
				$idthing = $value['idthing'];
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$databaseData['data'][$key]['isPartOf'] = $this->getIsPartOf($idthing);
				}
			}
		}

		//
		return $databaseData;
	}
}
