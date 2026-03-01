<?php
namespace Plinct\Api\Support;

use Plinct\Api\ApiFactory;
use Plinct\Api\Response\Type\TypeSchema;

class ParseToSchema
{
	/**
	 * @var string
	 */
	private string $type;
	/**
	 * @var array|null
	 */
	private ?array $data = [];

	private ?array $params = [];

	/**
	 * @param string $type
	 * @return ParseToSchema
	 */
	public function setType(string $type): static
	{
		$this->type = lcfirst($type);
		return $this;
	}

	/**
	 * @param ?array $data
	 * @return ParseToSchema
	 */
	public function setData(?array $data): ParseToSchema
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * @param array|null $params
	 * @return ParseToSchema
	 */
	public function setParams(?array $params): ParseToSchema
	{
		$this->params = $params;
		return $this;
	}

	/**
	 * @return array|null
	 */
	public function ready(): ?array
	{
		$format = $this->params['format'] ?? null;
		if (empty($this->data)) {
			return [];
		} else {
			if (isset($this->data['error']) || (isset($this->data['status']) && $this->data['status'] === 'error')) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($this->data);
			} elseif (isset($this->data['status']) && $this->data['status'] === 'fail') {
				return $this->data;
			} else {
				$newData = [];
				if (isset($this->data[0])) {
					foreach ($this->data as $value) {
						$typeSchema = new TypeSchema($this->type);
						$typeSchema->setValue($value);
						$newData[] = $typeSchema->ready();
					}
				} else {
					$typeSchema = new TypeSchema($this->type);
					$typeSchema->setValue($this->data);
					$newData = $typeSchema->ready();
				}
				// ITEM LIST
				if ($format == 'ItemList') {
					$listItem = [
						'@context'=>'https://schema.org',
						'@type'=>'ItemList',
						'itemListOrder' => $this->params['ordering'] ?? 'ascending',
						'numberOfItems'=>count($newData),
						'itemListElement'=>[]
					];
					foreach ($newData as $key => $item) {
						$listItem['itemListElement'][] = [
							'@type'=>'ListItem',
							'position'=>($key + 1),
							'item'=> $item
						];
					}
					return $listItem;
				}
				return $newData;
			}
		}
	}
}
