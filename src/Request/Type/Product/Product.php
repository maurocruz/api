<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\Product;

use Plinct\Api\Request\Server\Entity;

class Product extends Entity
{
  public function __construct()
	{
		$this->setTable('product');
	}

  public function get(array $params = []): array
  {
	  $orderBy = $params['orderBy'] ?? null;
	  if ($orderBy == 'dateModified') {
		  $returns = parent::getThingFirst('product', $params);
	 } else {
		  $returns = parent::getData($params);
	  }
	 return parent::sortData($returns);
 }

	/**
	 * @param array|null $params
	 * @return string[]
	 */
	public function post(?array $params = null): array
	{
		$params['type'][] = 'Product';
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
