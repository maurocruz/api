<?php
namespace Plinct\Api\Request\Configuration;

use Plinct\Api\ApiFactory;

class Home
{
	public function ready(): array
	{
		$host = ApiFactory::request()->configuration()->getHost();
		$API_HOST = $host."/api";
		$graph = [];
		$modulesAvailable = [
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
		// MODULES ENABLED
		$dataModulesEnabled = ApiFactory::request()->server()->connectBd()->showTables();
		// WEB API
		$graph[] = [
			"@type" => "WebAPI",
			"@id" => "$API_HOST#api",
			"name" => "Plinct API",
			"description" => "API baseada em schema.org com módulos habilitáveis",
			"documentation" => "$API_HOST/docs",
			"isSimilarTo" => array_map(fn($module) => ["@id" => "$API_HOST/api/$module"], $modulesAvailable)
		];
		// ENTRY POINT
		$graph[] = [
			"@type" => "EntryPoint",
			"@id" => "$host/api",
			"url" => "$host/api",
			"name" => "Plinct API",
			"description" => "Ponto de entrada principal da Plinct API"
		];
		// MODULES
		$modulesEnabledArray = array_map(fn($item) => strtolower($item['Tables_in_plinct_db']),$dataModulesEnabled);
		foreach ($modulesAvailable as $module) {
			$graph[] = [
				"@type" => "Service",
				"@id" => "$API_HOST/api/$module",
				"name" => $module,
				"offers" => in_array(strtolower($module), $modulesEnabledArray) ? "InStock" : "OutOfStock"
			];
		}

		return [
			"@context" => "https://schema.org",
			"@graph" => $graph
		];
	}

}
