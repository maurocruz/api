<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use Plinct\Api\Application\Support\SupportApplication;
use ReflectionException;

class ContactPointReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = null): array
	{
		// SANITIZE PARAMS
		$sanitizedParams = SupportApplication::sanitizeParams('ContactPoint', $queryParams);

		// SE HOUVER IDHASPART
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		if ($idHasPart !== null) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf'=>'ContactPoint']);
			$getData->setJoin('thing','thing.idthing=thing_has_thing.idIsPartOf');
			$getData->setJoin('contactPoint','contactPoint.thing=thing_has_thing.idIsPartOf');
			$data = $getData->render();
			if ($data['status'] === false) {
				return $data;
			}
			return SupportApplication::returnDTOFromDatabaseData($data);
		}

		// OBTEM DADOS DA BASE DE DADOS
		$data = $this->readEntity('contactPoint', $sanitizedParams);
		if ($data['status'] === false) {
			return $data;
		}
		return SupportApplication::returnDTOFromDatabaseData($data);
	}
}
