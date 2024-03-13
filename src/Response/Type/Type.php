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
	public function setData(?array $data): Type
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
			return [];
		} else {
			if (isset($this->data['error']) || (isset($this->data['status']) && $this->data['status'] === 'error')) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($this->data);
			} elseif (isset($this->data['status']) && $this->data['status'] === 'fail') {
				return $this->data;
			} else {
				$newData = [];
				foreach ($this->data as $value) {
					$typeSchema = new TypeSchema($this->type);
					$typeSchema->setValue($value);
					$newData[] = $typeSchema->ready();
				}
				return $newData;
			}
		}
	}
}
