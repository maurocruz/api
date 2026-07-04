<?php
namespace Plinct\Api\Domain\Modules;

use Plinct\Api\Domain\Entities\Entities;
use ReflectionClass;
use ReflectionException;

class ModuleDomain extends Entities
{
	private static array $modulesEnabled = [];

	/**
	 */
	public static function getModulesAvailable(): array
	{
		$modulesAvailable = [];
		foreach (parent::ENTITIES as $entity) {
			if (class_exists($entity['object'])) {
				$object = new $entity['object']();
				if ($object::IS_MODULE) {
					$modulesAvailable[] = $entity;
				}
			}
		}
		return $modulesAvailable;
	}
	/**
	 * @param array $modulesEnabled
	 */
	public static function setModulesEnabled(array $modulesEnabled): void
	{
		self::$modulesEnabled = $modulesEnabled;
	}

	/**
	 * @return array
	 */
	public static function getModulesEnabled(): array
	{
		return self::$modulesEnabled;
	}

	/**
	 * @return array
	 */
	public static function getNamesOfModulesAvailable(): array
	{
		$moduleNames = [];
		foreach (self::getModulesAvailable() as $module) {
			$moduleNames[] = $module['name'];
		}

		return $moduleNames;
	}

	public static function moduleExists(string $moduleName): bool
	{
		return in_array(ucfirst($moduleName), self::getNamesOfModulesAvailable(), true);
	}

	public static function moduleIsEnabled(string $moduleName): bool
	{
		return in_array(ucfirst($moduleName), self::getModulesEnabled(), true);
	}

	public static function getModuleNamespace(string $moduleName): ?string
	{
		foreach (self::ENTITIES as $module) {
			if ($module['name'] === ucfirst($moduleName)) {
			  return $module['object'] ?? null;
			}
		}
		return null;
	}

	/**
	 * @throws ReflectionException
	 */
	public static function getReflexionClass(string $moduleName): ?ReflectionClass
	{
		$moduleNamespace = self::getModuleNamespace($moduleName);
		return $moduleNamespace ? new ReflectionClass($moduleNamespace) : null;
	}

	/**
	 * @throws ReflectionException
	 */
	public static function getModuleClass(string $moduleName): object|string
	{
		$moduleNamespace = self::getModuleNamespace($moduleName);
		$reflection = new ReflectionClass($moduleNamespace);
		return $reflection->newInstanceWithoutConstructor();
	}

	public static function getModuleClassPath(string $moduleName): string
	{
		$moduleNamespace = self::getModuleNamespace($moduleName);
		return str_replace('\\', '/', $moduleNamespace);
	}

	public static function getModuleClassPathNamespace(string $moduleName): string
	{
		$moduleNamespace = self::getModuleNamespace($moduleName);
		return str_replace('\\', '\\\\', $moduleNamespace);
	}

	/**
	 * @throws ReflectionException
	 */
	public static function isModule(string $moduleName): bool
	{
		$moduleClass = self::getReflexionClass($moduleName);
		$moduleNamespace = self::getModuleNamespace($moduleName);
		return $moduleNamespace && $moduleClass->hasConstant('IS_MODULE') ? $moduleNamespace::IS_MODULE : false;
	}
}
