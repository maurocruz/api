<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPageElement;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use ReflectionException;

class WebPageElementReadUseCase extends CreativeWorkReadUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		$orderBy = $sanitizedParams['orderBy'] ?? null;
		$ordering = $sanitizedParams['ordering'] ?? "asc";
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf' => 'WebPageElement']);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
			$getData->setJoin('webPageElement', 'webPageElement.thing = thing_has_thing.idIsPartOf');
			if ($orderBy) {
				$getData->setParams(['orderBy' => $orderBy, 'ordering' => $ordering]);
			}
		} else {
			$getData = $this->databaseActions->read('webPageElement', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = webPageElement.thing');
		}
		$databaseData = $getData->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		// IF PROPERTIES
		$properties = $sanitizedParams['properties'] ?? [];

		foreach ($databaseData['data'] as $key => $webPage) {
			$idthing = $webPage['idthing'];

			// HAS PROPERTY VALUE
			$dataPropertiesValue = $this->getPropertiesValue($idthing);
			if ($dataPropertiesValue['status'] === false) {
				return $dataPropertiesValue;
			}
			$databaseData['data'][$key]['identifier'] = $dataPropertiesValue['data'];

			// IS PART OF THE WEB PAGE
			if (in_array('isPartOf', $properties)) {
				$databaseData['data'][$key]['isPartOf'] = $this->getIsPartOf($idthing);
			}

			// HAS PART
			if (in_array('hasPart', $properties)) {
				$paramsHasPart['idHasPart'] = $idthing;
				$paramsHasPart['typeHasPart'] = 'WebPageElement';
				$paramsHasPart['orderBy'] = $sanitizedParams['orderBy'] ?? 'thing.name';
				$dataGetHasPart = $this->databaseActions->read('thing_has_thing', $paramsHasPart);
				$dataGetHasPart->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
				$dataHasPart = $dataGetHasPart->render();
				if ($dataHasPart['status'] === false) {
					return $dataHasPart;
				}
				foreach ($dataHasPart['data'] as $key2 => $value) {
					$type = $value['type'];
					$thing = $value['idthing'];
					$dataThing = $this->databaseActions->read(lcfirst($type), ['thing' => $thing])->render();
					if ($dataThing['status'] === false) {
						return $dataThing;
					}
					$dataHasPart['data'][$key2] = array_merge($dataHasPart['data'][$key2], $dataThing['data'][0]);
				}
				$databaseData['data'][$key]['hasPart'] = $dataHasPart['data'] ?? null;
			}
		}
		return $databaseData;
	}
}
