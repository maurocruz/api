<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type;

use Plinct\Api\ApiFactory;

class Type extends TypeAbstract
{
	/**
	 * @var string
	 */
	private string $type;
	/**
	 * @var array|null
	 */
	private ?array $data = [];

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$this->type = lcfirst($type);
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
		} else {
			$className = __NAMESPACE__."\\".ucfirst($this->type)."\\".ucfirst($this->type);
			if(class_exists($className)) {
				$newData = [];
				foreach ($this->data as $value) {
					$typeObject = new $className();
					$typeObject->setContextSchema($this->type);
					$typeObject->get($value);
					$newData[] = $typeObject->ready();
				}
				return $newData;
			}
			return $this->data;
		}
	}
}
