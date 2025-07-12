<?php
namespace Plinct\Api\Request\Type\Product;

use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

class Product extends Entity
{
	/**
	 *
	 */
  public function __construct()
	{
		$this->setTable('product');
	}

	/**
	 * @param array $params
	 * @return array
	 */
  public function get(array $params = []): array
  {
		$dataGet = new GetData('product');
		$dataGet->setParams($params);
		$data = $dataGet->render();

	  return parent::sortData($data);
 }

	/**
	 * @param array|null $params
	 * @return string[]
	 */
	public function post(?array $params = null): array
	{
		$manufacturer = $params['manufacturer'] ?? null;
		if ($manufacturer == '') unset($params['manufacturer']);
		return parent::createWithParent('thing', $params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('thing', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('thing', $params);
	}
}
