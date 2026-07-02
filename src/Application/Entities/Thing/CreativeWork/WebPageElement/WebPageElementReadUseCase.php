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
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf' => 'WebPageElement']);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
			$getData->setJoin('webPageElement', 'webPageElement.thing = thing_has_thing.idIsPartOf');
			if ($orderBy) {
				$getData->setParams(['orderBy' => $orderBy]);
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
				$databaseData['data'][$key]['isPartOf'] = $this->isPartOf($idthing);
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

	/**
	 * @throws ReflectionException
	 */
	private function isPartOf(string $idthing)
	{
		$relationshipData = parent::getHasPart($idthing, 'webPageElement', 'WebPage');

		if ($relationshipData['status'] === false) {
			return $relationshipData;
		}
		// OBTEM WEBSITE COMO PARTE DE WEB PAGE
		if (!empty($relationshipData['data'])) {
			$valueWebPage = $relationshipData['data'][0];
			$webPageThing = $valueWebPage['thing'];
			$webSiteData = $this->databaseActions->read('thing_has_thing', ['idIsPartOf' => $webPageThing, 'typeIsPartOf' => 'WebPage'])
				->setJoin('thing', 'thing.idthing = thing_has_thing.idHasPart')
				->setJoin('webSite', 'webSite.thing = thing_has_thing.idHasPart')
				->render();
			if ($webSiteData['status'] === false) {
				return $webSiteData;
			}
			$valueWebSite = $webSiteData['data'];
			$relationshipData['data'][0]['isPartOf'] = $valueWebSite;
		}
		return $relationshipData['data'];
	}
}
