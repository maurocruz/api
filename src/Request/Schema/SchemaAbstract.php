<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Schema;

class SchemaAbstract
{
	const SCHEMA_PLINCT = __DIR__.'/schemasJson/plinct.json';
	const SCHEMA_ORG = __DIR__.'/schemasJson/schemaorg-current-http.jsonld';
	/**
	 * @var array
	 */
	protected array $schema = [
		"@context" => [
			"rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
			"rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
			"xsd" => "http://www.w3.org/2001/XMLSchema#"
		],
		"@graph" => []
	];
	/**
	 * @var array
	 */
	protected array $graph;
	/**
	 * @var string|null
	 */
	protected ?string $class;
	/**
	 * @var string|null
	 */
	protected ?string $subClass;
	/**
	 * @var string|null
	 */
	protected ?string $subClassOf;
	/**
	 * @var string|null
	 */
	protected ?string $schemas;

	/**
	 * @param array $graph
	 */
	protected function setGraph(array $graph): void
	{
		$this->schema['@graph'] = $graph;
		$this->graph = $graph;
	}

	/**
	 * @param string $filename
	 * @return void
	 */
	protected function appendSchema(string $filename): void
	{
		$schema = json_decode(file_get_contents($filename), true);
		$this->setGraph($schema['@graph']);
	}

	/**
	 * @return void
	 */
	protected function mergeSchemas(): void
	{
		$graph = [];

		if (!$this->schemas || str_contains($this->schemas, 'plinct')) {
			$soloineSchema =  json_decode(file_get_contents(self::SCHEMA_PLINCT), true);
			$this->schema['@context']['schema'][] = $soloineSchema['@context']['schema'];
			$graph = array_merge($graph, $soloineSchema['@graph']);
		}

		if (!$this->schemas || str_contains($this->schemas, 'schemaorg')) {
			$schemaorgSchema =  json_decode(file_get_contents(self::SCHEMA_ORG), true);
			$this->schema['@context']['schema'][] = $schemaorgSchema['@context']['schema'];
			$graph = array_merge($graph, $schemaorgSchema['@graph']);
		}

		$this->setGraph($graph);
	}
}