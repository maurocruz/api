<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Book extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('book');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = [];
		$dataBook = parent::getData($params);
		if (!empty($dataBook)) {
			foreach ($dataBook as $value) {
				// CREATIVE WORK
				$idcreativeWork = $value['creativeWork'];
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$idcreativeWork])->ready();
				// RESPONSE
				if (isset($dataCreativeWork[0])) {
					$returns[] = $value + $dataCreativeWork[0];
				} else {
					$returns[] = $value;
				}
			}
			return parent::sortData($returns);
		} else {
			return [];
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$params['type'][] = "Book";
		return parent::createWithParent('creativeWork',$params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::update('creativeWork', $params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('creativeWork', $params);
	}
}
