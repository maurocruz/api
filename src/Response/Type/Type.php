<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type;

use Plinct\Api\ApiFactory;

class Type
{
	/**
	 * @var mixed
	 */
	private $typeObject;
	/**
	 * @var array|null
	 */
	private ?array $data = [];

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$className = __NAMESPACE__."\\".ucfirst($type)."\\".ucfirst($type);
		if(class_exists($className)) {
			$this->typeObject = new $className();
		}
	}

	/**
	 * @param array $data
	 * @return $this
	 */
	public function get(array $data): Type
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function ready(): ?array
	{
		if (empty($this->data)) {
			return ApiFactory::response()->message()->fail()->returnIsEmpty();
		} else if (!!$this->typeObject) {
			return  $this->typeObject->get($this->data)->ready();
		} else {
			return $this->data;
		}
	}
}
