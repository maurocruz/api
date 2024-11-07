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
		$dataBook = [];
		$orderBy = $params['orderBy'] ?? null;
		if ($orderBy) {
			$dataThing = ApiFactory::request()->type('thing')->get(['type'=>'Book'] + $params)->ready();
			if (!empty($dataThing)) {
				foreach ($dataThing as $key => $thing) {
					$idthing = $thing['idthing'];
					$dataItem = parent::getData(['thing'=>$idthing] + $params);
					if (!empty($dataItem)) {
						$dataBook[$key] = $dataItem[0];
					}
				}
			}
		} else {
			$dataBook = parent::getData($params);
		}

		if (!empty($dataBook)) {
			foreach ($dataBook as $key => $value) {
				// CREATIVE WORK
				$idcreativeWork = $value['creativeWork'];
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork' => $idcreativeWork])->ready();
				// RESPONSE
				if (isset($dataCreativeWork[0])) {
					$dataBook[$key] = $value + $dataCreativeWork[0];
				} else {
					$dataBook[$key] = $value;
				}
			}
		}
		return parent::sortData($dataBook);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
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
