<?php
namespace Plinct\Api\Application\Support;

use Plinct\Api\Domain\Configuration\ConfigurationDomain;

class ConvertToSchemaOrg
{
	private const string SCHEMA_ORG_URL = 'https://schema.org/';

	private array $result;

	/**
	 */
	public function __construct(array $data)
	{
		$result = [];
		if (isset($data[0])) {
			foreach ($data as $value) {
				$result[] = $this->buildSchema($value);
			}
		} else {
			$result[] = $this->buildSchema($data);
		}
		$this->result = $result;
	}

	public function buildSchema(array $value): array
	{

		$identifiers = $value['identifiers'];
		unset($value['identifiers']);
		$idthing = $identifiers['idthing'];
		$id = ConfigurationDomain::getHost().'/schema/'.lcfirst($value['type']).'/'.$idthing;

		$schema['@context'] = self::SCHEMA_ORG_URL;
		$schema['@type'] = $value['type'];
		$schema['@id'] = $id;
		// ITEMS
		ksort($value);
		foreach ($value as $key => $item) {
			$schema[$key] = $item;
		}
		// IDENTIFIERS
		if ($identifiers) {
			foreach ($identifiers as $key => $item) {
				$schema['identifier'][] = [
					'@type' => 'PropertyValue',
					'name' => $key,
					'value' => $item
				];
			}
		}
		return $schema;
	}

	public function convert(): array
	{
		return $this->result;
	}
}
