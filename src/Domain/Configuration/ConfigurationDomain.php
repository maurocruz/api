<?php
namespace Plinct\Api\Domain\Configuration;

use Firebase\JWT\JWT;
use Plinct\Api\Domain\Modules\ModuleDomain;

class ConfigurationDomain
{
	private const string API_TYPE = 'WebApi';
	private const string NAME = 'Plinct API';
	private const string DESCRIPTION = 'API baseada em schema.org com módulos habilitáveis';
	private const string DOCUMENTATION = "https://plinct.com.br/api/docs";
	private const string SCHEMA_ORG = "https://schema.org";

	private static string $host;

	public static function setHost(string $host): void
	{
		self::$host = $host;
	}

	public static function getHost(): string
	{
		return self::$host;
	}

	/**
	 * @param int $iduser
	 * @param string $name
	 * @return string
	 */
	public static function createAuthToken(int $iduser, string $name): string
	{
		$payload = [
			"iss" => self::getHost(),
			"exp" => time() + $_ENV['JWT_EXPIRATION'],
			"name" => $name,
			"uid" => $iduser
		];
		return JWT::encode($payload, $_ENV['JWT_SECRET'],'HS256');
	}


	public static function verbose(): array
	{
		$apiHost = self::getHost()."/api";
		// WEB API
		$graph[] = [
			"@type" => self::API_TYPE,
			"name" => self::NAME,
			"description" => self::DESCRIPTION,
			"documentation" => self::DOCUMENTATION,
			"@id" => $apiHost . "#api",
			"isSimilarTo" => array_map(fn($module) => ["@id" => $apiHost ."/" . lcfirst($module)."#module"], ModuleDomain::getNamesOfModulesAvailable())
		];
		// ENTRY POINT
		$graph[] = [
			"@type" => "EntryPoint",
			"@id" => $apiHost,
			"url" => $apiHost,
			"name" => self::NAME,
			"description" => "Ponto de entrada principal da Plinct API"
		];
		// MODULES
		foreach (ModuleDomain::getNamesOfModulesAvailable() as $module) {
			$graph[] = [
				"@type" => "Service",
				"@id" => ConfigurationDomain::getHost()."/api/".lcfirst($module)."#module",
				"name" => $module,
				"offers" => in_array($module, ModuleDomain::getModulesEnabled()) ? "InStock" : "OutOfStock"
			];
		}
		// RETURN
		return [
			"@context" => self::SCHEMA_ORG,
			"@graph" => $graph
		];
	}
}
