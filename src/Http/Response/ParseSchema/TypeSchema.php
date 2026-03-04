<?php
namespace Plinct\Api\Http\Response\ParseSchema;

use Plinct\Api\Domain\Configuration\ConfigurationDomain;

class TypeSchema extends TypeSchemaAbstract
{
	/**
	 * @var array|null
	 */
	private ?array $value;

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$this->setContextSchema($type);
	}

	/**
	 * @param array|null $value
	 * @return TypeSchema
	 */
	public function setValue(?array $value): TypeSchema
	{
		$host = ConfigurationDomain::getHost();
		// SET @TYPE AND @ID
		if ($value['type'] ?? $this->type) {
			$value['@type'] = $value['type'] ?? $this->type;
			$idname = 'id'.lcfirst($value['type'] ?? $this->type);
			$idnumber = $value['idthing'] ?? $value['thing'] ?? $value[$idname] ?? null;
			if ($idnumber) {
				$value = ['@id'=>$host.'/schema/'.lcfirst($value['@type']).'/'.$idnumber] + $value;
			}
		}
		foreach ($value as $key => $valueItem) {
			if ($valueItem === null) {
				unset($value[$key]);
			} else {
				if(is_string($key) && str_starts_with($key, 'id') && !is_array($valueItem)) {
					$this->setIdentifier($key, (string) $valueItem);
					unset($value[$key]);
				}
				if ($key === 'dateRegistered' || $key === 'lastModified') {
					$this->setIdentifier($key, $valueItem);
				}
			}
		}
		unset($value['type']);
		unset($value['thing']);
		unset($value['mediaObject']);
		unset($value['creativeWork']);
		unset($value['dateRegistered']);
		unset($value['lastModified']);
		$this->value = $value;
		return $this;
	}

	/**
	 * @return array
	 */
	public function ready(): array
	{
		if (array_key_exists('identifier', $this->value)) {
			$this->identifier = array_merge($this->identifier, $this->value['identifier']);
		}
		return array_merge($this->contextSchema, $this->thingData, $this->value, !empty($this->identifier) ? ['identifier'=>$this->identifier] : []);
	}
}
