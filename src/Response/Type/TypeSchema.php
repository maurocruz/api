<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type;

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
		if (isset($value['type'])) {
			$value['@type'] = $value['type'];
		}
		foreach ($value as $key => $valueItem) {
			if(is_string($key) && substr($key,0,2) === 'id' && !is_array($valueItem)) {
				$this->setIdentifier($key, (string) $valueItem);
				unset($value[$key]);
			}
			if ($key === 'dateCreated' || $key === 'dateModified') {
				$this->setIdentifier($key, $valueItem);
			}
		}
		unset($value['type']);
		unset($value['thing']);
		unset($value['mediaObject']);
		unset($value['creativeWork']);
		unset($value['dateCreated']);
		unset($value['dateModified']);

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
		return array_merge($this->contextSchema, $this->thingData, $this->value, ['identifier'=>$this->identifier]);
	}
}
