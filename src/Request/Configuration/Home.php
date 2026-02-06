<?php

namespace Plinct\Api\Request\Configuration;

use Plinct\Api\ApiFactory;

class Home
{
	public function ready(): array
	{
		$host = ApiFactory::request()->configuration()->getHost();
		$API_HOST = $host."/api";
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
		return [
			"@context" => "https://schema.org",
			"@graph" => [
				[
					"@type" => "WebAPI",
					"@id" => "$API_HOST#api",
					"name" => "Plinct API",
					"description" => "API baseada em schema.org com módulos habilitáveis",
					"documentation" => "$API_HOST/docs",
					"hasPart" => [
						array_map(fn($module) => ["@id" => "$API_HOST/api/$module"], $modulesAvailable)
          ]
				],
				[
					"@type" => "EntryPoint",
					"@id" => "$host/api",
					"url" => "$host/api",
					"name" => "Plinct API",
					"description" => "Ponto de entrada principal da Plinct API"
				]
			]
		];
	}

}
