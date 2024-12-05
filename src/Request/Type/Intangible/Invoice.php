<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\Request\Server\Entity;

class Invoice extends Entity
{
	public function __construct()
	{
		$this->setTable('invoice');
	}

	/**
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
		$data = parent::getData($params);
    return parent::sortData($data);
  }
}
