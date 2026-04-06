<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPageElement;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class WebPageElementReadUseCase extends CreativeWorkReadUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		$sanitizedParams = SupportApplication::sanitizeParams('WebPageElement', $queryParams);

		$isPartOf = $sanitizedParams['isPartOf'] ?? null;
		if ($isPartOf) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $isPartOf, 'typeHasPart' => 'WebPage', 'typeIsPartOf' => 'WebPageElement']);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('webPageElement', 'webPageElement.thing = thing_has_thing.idIsPartOf');
		} else {
			$getData = $this->databaseActions->read('webPageElement', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = webPageElement.thing');
		}
		$databaseData = $getData->render();
		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		// IF PROPERTIES
		$properties = $sanitizedParams['properties'] ?? null;
		if ($properties) {
			foreach ($databaseData['data'] as $key => $webPage) {
				$idthing = $webPage['idthing'];
				// IS PART OF THE WEB PAGE
				if (in_array('isPartOf', $properties)) {
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
					$databaseData['data'][$key]['isPartOf'] = $relationshipData['data'];
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
		}
		return SupportApplication::returnDTOFromDatabaseData($databaseData,  'WebPageElement found');
	}
}
