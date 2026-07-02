<?php
namespace Plinct\Api\Application\Entities\Thing\Intangible\StructuredValue\PropertyValue;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use ReflectionException;

class PropertyValueReadUseCase extends ThingReadUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function read(array $sanitizedParams): array
	{
		$idHasPart = $sanitizedParams['idHasPart'] ?? null;
		if ($idHasPart) {
			$getData = $this->databaseActions->read('thing_has_thing', ['idHasPart' => $idHasPart, 'typeIsPartOf' => 'PropertyValue']);
			$getData->setJoin('thing', 'thing.idthing = thing_has_thing.idIsPartOf');
			$getData->setJoin('propertyValue', 'propertyValue.thing = thing_has_thing.idIsPartOf');
			$data = $getData->render();
		} else {
			$data = $this->readEntity('PropertyValue', $sanitizedParams);
		}
		return $data;
	}
}
