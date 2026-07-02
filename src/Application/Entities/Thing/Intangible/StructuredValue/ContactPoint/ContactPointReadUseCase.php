<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\ContactPoint;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use ReflectionException;

class ContactPointReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		// SE HOUVER IDHASPART
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		if ($idHasPart !== null) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf'=>'ContactPoint']);
			$getData->setJoin('thing','thing.idthing=thing_has_thing.idIsPartOf');
			$getData->setJoin('contactPoint','contactPoint.thing=thing_has_thing.idIsPartOf');
			return $getData->render();
		}

		// OBTEM DADOS DA BASE DE DADOS
		return $this->readEntity('contactPoint', $sanitizedParams);
	}
}
