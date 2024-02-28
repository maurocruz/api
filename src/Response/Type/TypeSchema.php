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
	 * @return void
	 */
	public function get(?array $value): void
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
			$value['contactPoint'] = ApiFactory::response()->type('contactPoint')->get($value['contactPoint'])->ready();
		}
		if (array_key_exists('image',$value)) {
			$value['image'] = ApiFactory::response()->type('imageObject')->get($value['image'])->ready();
		}
		$this->value = $value;
	}

	/**
	 * @return array
	 */
	public function ready(): array
	{
		return array_merge($this->contextSchema, $this->thingData, $this->value, ['identifier'=>$this->identifier]);
	}
}
