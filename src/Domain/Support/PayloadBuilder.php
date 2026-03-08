<?php
namespace Plinct\Api\Domain\Support;

class PayloadBuilder
{
	public static function build(object $entity): array
	{
		$payload = [];

		foreach($entity->getProperties() as $property) {

			// EXCLUI PROPRIEDADES PRIVADAS
			if ($property->isPrivate()) continue;

			// EXCLUI PROPRIEDADES NÃO INICIALIZADAS
			if (!$property->isInitialized($entity)) continue;

			// EXCLUI PROPRIEDADES PROTEGIDAS
			if ($property->isProtected()) continue;

			// LÊ VALOR DA PROPRIEDADE
			$value = $property->getValue($entity);

			// ESCREVE NO ARRAY PAYLOAD
			if ($value !== null) {
				$payload[$property->getName()] = $value;
			}
		}

		return $payload;
	}
}
