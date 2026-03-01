<?php
namespace Plinct\Api\Domain\Configuration;

use Plinct\Api\Domain\Modules\ModuleDomain;

class ConfigurationDomain
{
	private const string API_TYPE = 'WebApi';
	private const string NAME = 'Plinct API';
	private const string DESCRIPTION = 'API baseada em schema.org com módulos habilitáveis';
	private const string DOCUMENTATION = "https://plinct.com.br/api/docs";
	private const string SCHEMA_ORG = "https://schema.org";

	private array $modulesEnabled;
	private ?string $apiHost;

	public function __construct(string $apiHost, array $modulesEnabled)
	{
		$this->modulesEnabled = $modulesEnabled;
		$this->apiHost = $apiHost;
	}

	public static function entryPoint(string $apiHost): array {
		return [
			"@type" => "EntryPoint",
			"@id" => $apiHost,
			"url" => $apiHost,
			"name" => self::NAME,
			"description" => "Ponto de entrada principal da Plinct API"
		];
	}
	public static function getConfiguration(): array
	{
		return [
			"@type" => self::API_TYPE,
			"name" => self::NAME,
			"description" => self::DESCRIPTION,
			"documentation" => self::DOCUMENTATION
		];
	}

	public function buildGraphModulesEnabled(): array
	{
		$graph = [];
		foreach ($this->modulesEnabled as $module) {
			$graph[] = [
				"@type" => "Service",
				"@id" => "$this->apiHost.api/$module",
				"name" => $module,
				"offers" => in_array($module, $this->modulesEnabled) ? "InStock" : "OutOfStock"
			];
		}
		return $graph;
	}

	public static function addGraphInContext(array $graph): array
	{
		return [
			"@context" => self::SCHEMA_ORG,
			"@graph" => $graph
		];
	}
}
