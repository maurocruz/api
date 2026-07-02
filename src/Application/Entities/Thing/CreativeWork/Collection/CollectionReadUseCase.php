<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\Collection;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;

class CollectionReadUseCase extends CreativeWorkReadUseCase
{
	public function read(array $sanitizedParams = []): array
	{
		// ID HAS PART
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart] + $sanitizedParams);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
			$dataCollection = $getData->render();
			if ($dataCollection['status'] === false) {
				return $dataCollection;
			}
		} else {
			$getData = $this->databaseActions->read('collection', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = collection.thing');
			$dataCollection = $getData->render();
			if ($dataCollection['status'] === false) {
				return $dataCollection;
			}

			// PROPERTIES
			$properties = $sanitizedParams['properties'] ?? null;
			if ($properties) {
				foreach ($dataCollection['data'] as $key => $collection) {
					// HAS PART
					if (in_array('hasPart', $properties)) {
						$getDataHasPart = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $collection['thing']]);
						$getDataHasPart->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
						$dataHasPart = $getDataHasPart->render();
						if ($dataHasPart['status'] === false) {
							return $dataHasPart;
						}
						$dataCollection['data'][$key]['hasPart'] = $dataHasPart['data'];
					}
				}
			}
		}
		return $dataCollection;
	}
}
