<?php
namespace Plinct\Api\Request\Configuration;

use Plinct\Api\ApiApp;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Configuration\Module\ModuleController;
use Plinct\Api\Request\Configuration\Update\Update;

class Configuration
{

	public function index(string $dbName = null): array
	{
		// MODULES AVAILABLE
		$modulesAvailable = [
				'Article',
				'Book',
				'Certification',
				'Event',
				'ImageObject',
				'Order',
				'Organization',
				'Person',
				'Place',
				'Product',
				'Review',
				'Service',
				'Taxon',
				'VideoObject',
				'WebSite'
		];
		// MODULES ENABLED
		$dataModulesEnabled = ApiFactory::request()->server()->connectBd()->showTables();
    $dbName = $dbName ?? ApiApp::getDBNAME();
		$tablesInDb = [];
		$modulesEnabled = [];
		foreach ($dataModulesEnabled as $moduleEnable) {
			$tablesInDb[] = $moduleEnable["Tables_in_$dbName"];
		}
		foreach ($modulesAvailable as $module) {
			if (in_array(lcfirst($module),$tablesInDb)) {
				$modulesEnabled[] = $module;
			}
		}
		// RETURN
		return [
			'@context'=>'https://schema.org',
			'@type'=>'ItemList',
			'name' => 'Configuration items',
			'itemListElement' => [
 				[ 'item' => [ '@type'=>'ItemList', 'name' => 'Modules Available', 'itemListElement' => $modulesAvailable ]],
				[ 'item' => [ '@type'=>'ItemList', 'name' => 'Modules Enabled', 'itemListElement' => $modulesEnabled ]]
			]
		];
	}

	/**
	 * @return ModuleController
	 */
	public function module(): ModuleController
	{
		return new ModuleController();
	}

	/**
	 * @return Update
	 */
	public function update(): Update
	{
		return new Update();
	}

	/**
	 * @return string
	 */
	public function getHost(): string
	{
		return ApiApp::$HOST;
	}
}
