<?php
namespace Plinct\Api\Domain\Modules;

class ModuleDomain
{
	private const array MODULES_AVAILABLE = [
		'AudioObject',
		'Action',
		'Article',
		'Book',
		'Certification',
		'Collection',
		'CreativeWork',
		'Event',
		'ImageObject',
		'MediaObject',
		'Order',
		'Organization',
		'Person',
		'Place',
		'Product',
		'Review',
		'Role',
		'Service',
		'Taxon',
		'VideoObject',
		'WebSite'
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

	public static function getModulesAvailable(): array
	{
		return self::MODULES_AVAILABLE;
	}



}
