<?php
namespace Plinct\Api\Application\Config;

use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Domain\Modules\ModuleDomain;

readonly class HomeUseCase
{
	public function __construct(private ConfigUseCase $configUseCase)
	{
	}

	public function ready(): array
	{
		$API_HOST = $this->configUseCase->getHost()."api";
		$modulesAvailable = ModuleDomain::getModulesAvailable();
		$apiConfiguration = ConfigurationDomain::getConfiguration();
		$graph = [
			$apiConfiguration
		];
		// WEB API
		$graph[0]["@id"] = "$API_HOST#api";
		$graph[0]["isSimilarTo"] = array_map(fn($module) => ["@id" => "$API_HOST/api/$module"], $modulesAvailable);
		// ENTRY POINT
		$graph[] = ConfigurationDomain::entryPoint($API_HOST);
		// MODULES
		$graph = $this->configUseCase->getModulesForGraph($graph);
		// CONTEXT
		return ConfigurationDomain::addGraphInContext($graph);
	}
}
