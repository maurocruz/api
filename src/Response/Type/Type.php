<?php
declare(strict_types=1);
namespace Plinct\Api\Response\Type;

use Plinct\Api\ApiFactory;

class Type
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
	 * @param ?array $data
	 * @return $this
	 */
	public function get(?array $data): Type
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
			return $this->data === null ? null :  ApiFactory::response()->message()->success('No data found');
		} else {
			$newData = [];
			foreach ($this->data as $value) {
				$typeSchema = new TypeSchema($this->type);
				$typeSchema->get($value);
				$newData[] = $typeSchema->ready();
			}
			return $newData;
		}
	}
}
