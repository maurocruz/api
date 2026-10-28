<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use Plinct\Api\Application\Entities\Thing\CreativeWork\WebPageElement\WebPageElementReadUseCase;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

class WebPageReadUseCase extends CreativeWorkReadUseCase
{
	public function __construct(
		DatabaseActions $databaseActions,
		private readonly WebPageElementReadUseCase $webPageElementReadUseCase
	) {
		parent::__construct($databaseActions);
	}

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
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
		$properties = $sanitizedParams['properties'] ?? [];
		foreach ($databaseData['data'] as $key => $webPage) {
			$idthing = $webPage['idthing'];

			// HAS PROPERTY VALUE
			$dataPropertiesValue = $this->getPropertiesValue($idthing);
			if ($dataPropertiesValue['status'] === false) {
				return $dataPropertiesValue;
			}
			$databaseData['data'][$key]['identifier'] = $dataPropertiesValue['data'];

			// IS PART OF
			if (in_array('isPartOf', $properties)) {
				$relationshipData = parent::getIsPartOf($idthing);
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
				$dataGetHasPart->setWhere('typeIsPartOf <> "PropertyValue"');
				$dataHasPart = $dataGetHasPart->render();

				if ($dataHasPart['status'] === false) {
					return $dataHasPart;
				}
				foreach ($dataHasPart['data'] as $key2 => $value) {
					$type = $value['type'];
					$thing = $value['idthing'];
					if ($type === 'WebPageElement') {
						$dataItem = $this->webPageElementReadUseCase->read(['thing' => $thing]);
					} else {
						$getDataThing = $this->databaseActions->read(lcfirst($type), ['thing' => $thing]);
						$dataItem = $getDataThing->render();
					}
					if ($dataItem['status'] === false) {
						return $dataItem;
					}
					$dataHasPart['data'][$key2] = array_merge($dataHasPart['data'][$key2], $dataItem['data'][0]);
				}
				$databaseData['data'][$key]['hasPart'] = $dataHasPart['data'] ?? null;
			}
		}
		return $databaseData;
	}
}
