<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\Collection;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;

class CollectionReadUseCase extends CreativeWorkReadUseCase
{
	public function read(array $sanitizedParams = []): array
	{
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$isPartOf = $sanitizedParams['isPartOf'] ?? null;
		$typeIsPartOf = $sanitizedParams['typeIsPartOf'] ?? null;
		// ID HAS PART
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart] + $sanitizedParams);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
		} elseif($isPartOf) {
			if ($isPartOf === 'false') {
				unset($sanitizedParams['isPartOf']);
			}
			$getData = $this->databaseActions->read('collection', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = collection.thing');
			$getData->setJoin('thing_has_thing', 'thing_has_thing.idHasPart = collection.thing');
			$getData->setWhere('thing_has_thing.idIsPartOf <> collection.thing AND thing_has_thing.typeIsPartOf = "Collection"');
		} else {
			$getData = $this->databaseActions->read('collection', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = collection.thing');
		}
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
					unset($sanitizedParams['url']);
					unset($sanitizedParams['idcollection']);
					$getDataHasPart = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $collection['thing']] + $sanitizedParams);
					$getDataHasPart->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
					$getDataHasPart->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
					if ($typeIsPartOf) {
						$tableName = lcfirst($typeIsPartOf);
						if ($tableName === 'audioObject' || $tableName === 'imageObject' || $tableName === 'videoObject') {
							$getDataHasPart->setJoin('mediaObject', 'mediaObject.thing = creativeWork.thing');
						}
						$getDataHasPart->setJoin($tableName, "creativeWork.thing = $tableName.thing");
					}
					$dataHasPart = $getDataHasPart->render();
					if ($dataHasPart['status'] === false) {
						return $dataHasPart;
					}
					$dataCollection['data'][$key]['hasPart'] = $dataHasPart['data'];
				}
			}
		}
		return $dataCollection;
	}
}
