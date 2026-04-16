<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class WebPageReadUseCase extends CreativeWorkReadUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('WebPage',$queryParams);

		$orderBy = $sanitizedParams['orderBy'] ?? null;
		if ($orderBy === 'position') {
			unset($sanitizedParams['orderBy']);
		}

		// OBTÈM DADOS DO BANCO DE DADOS
		$idHasPart = $sanitizedParams['idHasPart'] ?? $sanitizedParams['isPartOf'] ?? null;
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf' => 'WebPage'])
				->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf')
				->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf')
				->setJoin('webPage', 'webPage.thing = thing_has_thing.idIsPartOf');
				if($orderBy) {
					$getData->setParams(['orderBy' => $orderBy]);
				}
		} else {
			$getData = $this->databaseActions->read('webPage', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = webPage.thing');
		}
		$databaseData = $getData->render();

		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		// IF PROPERTIES
		$properties = $sanitizedParams['properties'] ?? null;
		if (!empty($properties)) {
			foreach ($databaseData['data'] as $key => $webPage) {
				$idthing = $webPage['idthing'];

				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$relationshipData = parent::getHasPart($idthing, 'WebPage', 'WebSite');
					if ($relationshipData['status'] === false) {
						return $relationshipData;
					}
					$databaseData['data'][$key]['isPartOf'] = $relationshipData['data'];
				}

				// HAS PART
				if (in_array('hasPart', $properties)) {
					$paramsHasPart['idHasPart'] = $idthing;
					$paramsHasPart['typeHasPart'] = 'WebPage';
					$paramsHasPart['orderBy'] = $orderBy ?? 'thing.name';
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
		return SupportApplication::returnDTOFromDatabaseData($databaseData,  'WebPage found');
	}
}
