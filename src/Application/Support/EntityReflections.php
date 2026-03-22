<?php
namespace Plinct\Api\Application\Support;

use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\Modules\ModuleDomain;
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
			$classPathNamespace = ModuleDomain::getModuleNamespace($className);
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

	public function getReflection(): ReflectionClass
	{
		return $this->reflection;
	}

	public function getShortName(): string
	{
		return $this->reflection->getShortName();
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
			$type = $property->getType();

			if ($type instanceof ReflectionNamedType) {
				$typeName = $type->getName();
			} elseif ($type instanceof ReflectionUnionType) {
				$typeName = $type->getTypes()[0]->getName();
			} else {
				$typeName = null;
			}

			if (!array_key_exists($name, $database)) continue;
			$value = $database[$name];

			// SE A PROPRIEDADE É INT OU FLOAT E O VALOR FOR UMA STRING VAZIA, SETA O VALOR COMO NULL
			if ($value === '' && ($typeName === 'int' || $typeName === 'float' )) {
				$property->setValue($entity, null);
				continue;
			}
			$property->setValue($entity, self::castValue($value, $typeName));
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

			if (!array_key_exists($name, $params)) continue;

			$paramsArray[$name] = $params[$name];

		}
		return $paramsArray;
	}

	/**
	 * @throws ReflectionException
	 */
	public static function convertFromObjectToSchemaOrgArrayDTO(array $entities): array
	{
		$result = [];
		foreach ($entities as $key => $entity)
		{
			$result[$key]['@context'] = 'https://schema.org';
			$result[$key]['@type'] = $entity->getType();
			$result[$key]['@id'] = ConfigurationDomain::getHost() . '/schema/' . lcfirst($entity->getType()) . '/' . $entity->getIdthing();

			$reflection = new ReflectionClass($entity);

			foreach ($reflection->getProperties() as $property)
			{
				// EXCLUI PROPRIEDADES NAO INICIALIZADAS
				if (!$property->isInitialized($entity)) continue;

				// EXCLUI PROPRIEDADES PRIVADAS
				if ($property->isPrivate()) continue;

				$value = $property->getValue($entity);
				$name = $property->getName();

				// SEPARA PROPRIEDADES PROTEGIDAS DE PUBLICAS
				if ($property->isProtected()) {
					$result[$key]['identifier'][] = ['@type' => 'PropertyValue', 'name' => $name, 'value' => $value];
				}
				elseif ($property->isPublic()) {
					// SE PROPRIEDADE FOR OBJETO
					if (is_array($value)) {
						$result[$key][$name] = self::convertFromObjectToSchemaOrgArrayDTO($value);
					} else {
						$result[$key][$name] = $value;
					}
				}
			}
		}
		// ORDENA OS DADOS
		foreach ($result as $key => $value) {
			$identifiers = $value['identifier'];
			unset($value['identifier']);
			ksort($value);
			$value['identifier'] = $identifiers;
			$result[$key] = array_filter($value);
		}
		return $result;
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


}
