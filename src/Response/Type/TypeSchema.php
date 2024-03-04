<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type;

use Plinct\Api\ApiFactory;

class TypeSchema extends TypeSchemaAbstract
{
	/**
	 * @var string
	 */
	private string $type;
	/**
	 * @var array|null
	 */
	private ?array $value;

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$this->type = $type;
		$this->setContextSchema($type);
	}

	/**
	 * @param array|null $value
	 * @return TypeSchema
	 */
	public function setValue(?array $value): TypeSchema
	{
		$idname = "id".lcfirst($this->type);
		$this->setIdentifier($idname, (string) $value[$idname]);

		if (isset($value['thing']) && is_array($value['thing'])) {
			$this->setThingData($value['thing']);
		}
		unset($value[$idname]);
		unset($value['thing']);
		unset($value['idthing']);

		if (array_key_exists('contactPoint',$value)) {
			$value['contactPoint'] = ApiFactory::response()->type('contactPoint')->setData($value['contactPoint'])->ready();
		}
		if (array_key_exists('image',$value)) {
			$value['image'] = ApiFactory::response()->type('imageObject')->setData($value['image'])->ready();
		}
		if (array_key_exists('address',$value) && is_array($value['address'])) {
			$value['address'] = (new TypeSchema('postalAddress'))->setValue($value['address'])->ready();
		}
		if (array_key_exists('homeLocation',$value) && is_array($value['homeLocation'])) {
			$value['homeLocation'] = (new TypeSchema('place'))->setValue($value['homeLocation'])->ready();
		}

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
