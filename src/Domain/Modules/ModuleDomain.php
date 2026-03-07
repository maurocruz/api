<?php
namespace Plinct\Api\Domain\Modules;

class ModuleDomain
{
	private const array MODULES_AVAILABLE = [
		['name' => 'Action', 'dependencies' => ['Thing']],
		['name' => 'Article', 'dependencies' => ['Thing','CreativeWork']],
		['name' => 'AudioObject', 'dependencies' => ['Thing','CreativeWork','MediaObject']],
		['name' => 'Book', 'dependencies' => ['Thing','CreativeWork']],
		['name' => 'Certification', 'dependencies' => ['Thing','CreativeWork']],
		['name' => 'Collection', 'dependencies' => ['Thing','CreativeWork']],
		['name' => 'CreativeWork', 'dependencies' => ['Thing']],
		['name' => 'Event', 'dependencies' => ['Thing','Place']],
		['name' => 'ImageObject', 'dependencies' => ['Thing','CreativeWork','MediaObject']],
		['name' => 'MediaObject', 'dependencies' => ['Thing','CreativeWork']],
		['name' => 'Order', 'dependencies' => ['Thing','Offer','OrderItem','Invoice']],
		['name' => 'Organization', 'dependencies' => ['Thing','Place']],
		['name' => 'Person', 'dependencies' => ['Thing','PostalAddress','Place']],
		['name' => 'Place', 'dependencies' => ['Thing']],
		['name' => 'Product', 'dependencies' => ['Thing']],
		['name' => 'Review', 'dependencies' => ['Thing','CreativeWork']],
		['name' => 'Role', 'dependencies' => ['Thing','Person','Organization']],
		['name' => 'Service', 'dependencies' => ['Thing']],
		['name' => 'Taxon', 'dependencies' => ['Thing']],
		['name' => 'Thing', 'dependencies' => []],
		['name' => 'VideoObject', 'dependencies' => ['Thing','CreativeWork','MediaObject']],
		['name' => 'WebSite', 'dependencies' => ['Thing','CreativeWork','WebPage','WebPageElement']],
	];

	private static array $modulesEnabled = [];

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

	public static function getNamesOfModulesAvailable(): array
	{
		$moduleNames = [];
		foreach (self::MODULES_AVAILABLE as $module) {
			$moduleNames[] = $module['name'];
		}

		return $moduleNames;
	}

	public static function getModulesAvailable(): array
	{
		return self::MODULES_AVAILABLE;
	}

	public static function getDependenciesOfModule(string $moduleName): array
	{
		$dependencies = [];
		foreach (self::MODULES_AVAILABLE as $module) {
			if ($module['name'] === $moduleName) {
				$dependencies = $module['dependencies'];
			}
		}
		return $dependencies ?? [];
	}

	public static function moduleExists(string $moduleName): bool
	{
		return in_array($moduleName, self::getNamesOfModulesAvailable(), true);
	}

	public static function moduleIsEnabled(string $moduleName): bool
	{
		return in_array($moduleName, self::getModulesEnabled(), true);
	}

	public function isModuleEnabled(string $moduleName): bool
	{
		return $this->moduleIsEnabled($moduleName);
	}

	public function isModuleAvailable(string $moduleName): bool
	{
		return $this->moduleExists($moduleName);
	}
}
