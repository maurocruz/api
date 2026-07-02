<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Domain\Entities\Thing\CreativeWork\CreativeWork;
use ReflectionException;

class CreativeWorkReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{

		// IF ID HAS PART
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		if ($idHasPart) {
			// OBTER TODOS AS ENTIDADES QUE DEPENDEM DE CREATIVE WORK
			$creativeWorkChildren = CreativeWork::getChildren();
			$creativeWorkChildrenString = implode("','", $creativeWorkChildren);

			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart] + $sanitizedParams);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
			$getData->setWhere("`typeIsPartOf` IN ('$creativeWorkChildrenString')");
			$data = $getData->render();
			if ($data['status'] === false) {
				return $data;
			}
			foreach ($data['data'] as $key => $value) {
				$data['data'][$key]['type'] = $value['typeIsPartOf'];
				$data['data'][$key]['idthing'] = $value['idIsPartOf'];
			}
			return $data;
		}

		// IF IS PART OF
		$idIsPartOf = $sanitizedParams['idIsPartOf'] ?? null;
		if ($idIsPartOf) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idIsPartOf' => $idIsPartOf] + $sanitizedParams);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idHasPart');
			return $getData->render();
		}

		return $this->readEntity('CreativeWork', $sanitizedParams);
	}
}
