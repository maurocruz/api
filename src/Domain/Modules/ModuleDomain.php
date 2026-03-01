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
	private array $modulesEnabled = [];

	/**
	 * @param array $modulesEnabled
	 */
	public function setModulesEnabled(array $modulesEnabled): void
	{
		$this->modulesEnabled = $modulesEnabled;
	}

	/**
	 * @return array
	 */
	public function getModulesEnabled(): array
	{
		return $this->modulesEnabled;
	}

	public static function getModulesAvailable(): array
	{
		return self::MODULES_AVAILABLE;
	}



}
