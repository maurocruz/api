<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class Article extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('article');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = [];
		$dataArticle = parent::getData($params);
		if (!empty($dataArticle)) {
			foreach ($dataArticle as $value) {
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
		$params['type'][] = "Article";
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
