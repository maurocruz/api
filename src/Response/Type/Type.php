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

	private ?array $params = [];

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
	 * @param array|null $params
	 * @return $this
	 */
	public function setParams(?array $params): Type
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
				foreach ($this->data as $value) {
					$typeSchema = new TypeSchema($this->type);
					$typeSchema->setValue($value);
					$newData[] = $typeSchema->ready();
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
