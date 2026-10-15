<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebSite;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;

class WebSiteReadUseCase extends CreativeWorkReadUseCase
{
	public function read(array $sanitizedParams = []): array
	{
		$properties = $sanitizedParams['properties'] ?? null;

		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		$typeIsPartOf = $sanitizedParams['typeIsPartOf'] ?? null;

		if ($idIsPartOf) {
			$params['idIsPartOf'] = $idIsPartOf;
			if ($typeIsPartOf) {
				$params['typeIsPartOf'] = $typeIsPartOf;
			}
			$databaseData = $this->databaseActions->read('thing_has_thing', $params)
				->setJoin('thing', 'thing.idthing = thing_has_thing.idHasPart')
				->render();
		} else {
			// OBTÈM DADOS DO BANCO DE DADOS
			$getData = $this->databaseActions->read('webSite', $sanitizedParams);
			$getData->setJoin('creativeWork', 'creativeWork.thing = webSite.thing');
			$databaseData = $getData->render();
		}

		if ($databaseData['status'] === false) {
			return $databaseData;
		}

		if ($properties) {
			foreach ($databaseData['data'] as $key => $webSite) {
				$idthing = $webSite['idthing'];
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$paramsHasPart['idHasPart'] = $idthing;
					$paramsHasPart['typeHasPart'] = 'WebSite';
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
		return $databaseData;
	}
}
