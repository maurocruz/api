<?php
namespace Plinct\Api\Application\Support;

use Plinct\Api\Domain\Entities\Entities;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionUnionType;

class EntityReflections
{
	private static array $cache = [];
	private $reflection;

	/**
	 * @throws ReflectionException
	 */
	public function __construct(string $className)
	{
		if (class_exists($className)) {
			$classPathNamespace = $className;
		} else {
			$classPathNamespace = Entities::getEntity($className);
			if (!$classPathNamespace) {
				throw new ReflectionException("Class $className in Domain not found");
			}
		}

		if (isset(self::$cache[$classPathNamespace])) {
			$this->reflection = self::$cache[$classPathNamespace];
		} else {
			$this->reflection = new ReflectionClass($classPathNamespace);
			self::$cache[$classPathNamespace] = $this->reflection;
		}
	}

	/**
	 * @return array
	 */
	public function getOwnProperties(): array
	{
		return array_filter(
			$this->reflection->getProperties(),
			fn($prop) => $prop->getDeclaringClass()->getName() === $this->reflection->getName()
		);
	}

	/**
	 * @throws ReflectionException
	 */
	public function hydrateFromDatabase(array $database): ?object
	{
		// CRIA NOVA INSTANCIA
		$entity = $this->reflection->newInstanceWithoutConstructor();

		foreach ($this->reflection->getProperties() as $property) {
			$name = $property->getName();
			$type = self::getTypeName($property);

			if (!array_key_exists($name, $database)) continue;
			$value = $database[$name];

			// SE O VALOR FOR NULL, PULA
			if ($value === null) continue;

			// SE A PROPRIEDADE É INT OU FLOAT E O VALOR FOR UMA STRING VAZIA, SETA O VALOR COMO NULL
			if ($value === '' && ($type === 'int' || $type === 'float' )) {
				$property->setValue($entity, null);
				continue;
			}

			$property->setValue($entity, self::castValue($value, $type));
		}
		return  $entity;
	}

	/**
	 */
	public function hydrateOwnProperties(array $params, bool $withParent = false): array
	{
		$paramsArray = [];
		foreach ($this->reflection->getProperties() as $property) {
			// EXCLUI PROPRIEDADES DA CLASSE PAI
			if (!$withParent && $this->reflection->getName() !== $property->getDeclaringClass()->getName()) continue;

			$name = $property->getName();
			$type = self::getTypeName($property);

			// SE A PROPRIEDADE NAO EXISTE NO ARRAY DE PARAMS, PULA
			if (!array_key_exists($name, $params)) continue;

			// SE O TIPO FOR MIXED E O VALOR FOR NULL, PULA
			if ($type === 'mixed' && ($params[$name] === null || $params[$name] === '')) continue;

			// SETA O VALOR CASTADO
			$paramsArray[$name] = self::castValue($params[$name], $type);
		}
		return $paramsArray;
	}

	/**
	 * @param mixed $value
	 * @param string|null $type
	 * @return mixed
	 */
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

	private static function getTypeName($property): ?string
	{
		$type = $property->getType();
		if ($type instanceof ReflectionNamedType) {
			return $type->getName();
		} elseif ($type instanceof ReflectionUnionType) {
			return $type->getTypes()[0]->getName();
		} else {
			return null;
		}
	}
}
