<?php
namespace Plinct\Api\Domain\Support;

use ReflectionClass;
use ReflectionException;

class EntityHydrator
{
	/**
	 * @throws ReflectionException
	 */
	public static function fromArray(string $class, array $params): object|string
	{
		$reflection = new ReflectionClass($class);
		$entity = $reflection->newInstanceWithoutConstructor();

		foreach ($reflection->getProperties() as $property) {

			if ($property->getDeclaringClass()->getName() !== $class) continue;			$name = $property->getName();
			if (!array_key_exists($name, $params)) continue;

			$type = $property->getType();
			$value = $params[$name];

			// SE A PROPRIEDADE É INT OU FLOAT E O VALOR FOR UMA STRING VAZIA, SETA O VALOR COMO NULL
			if ($value === '' && ($property->getType()?->getName() === 'int' || $property->getType()?->getName() === 'float' )) {
				$property->setValue($entity, null);
				continue;
			}

			$property->setValue($entity, self::castValue($value, $type?->getName()));
		}
		return $entity;
	}

	private static function castValue(mixed $value, ?string $type): mixed
	{
		return match ($type) {
			'int' => (int) $value,
			'float' => (float) $value,
			'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
			'string' => (string) $value,
			'array' => (array) $value,
			default => $value
		};
	}
}
