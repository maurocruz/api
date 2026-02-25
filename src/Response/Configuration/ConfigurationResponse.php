<?php
namespace Plinct\Api\Response\Configuration;

use Plinct\Api\ApiFactory;

class ConfigurationResponse
{
	private ?array $data;

	public function __construct(array $data = [])
	{
		$this->data = $data;
	}

	public function index(): array
	{
		$apiHost = ApiFactory::request()->configuration()->getHost();
		$dbname = $this->data['dbName'];
		$numberOfTables = $this->data['numberOfTables'];
		$tables = $this->data['tables'];

		return [
			"@context" => "https://schema.org",
			"@graph" => [
				[
					"@type" => "Dataset",
					"@id" => $apiHost."/api/config#database",
					"name" => "Base de dados '$dbname'",
					"size" => $numberOfTables,
					"variableMeasured" => $tables
				]
			]
		];
	}
}
