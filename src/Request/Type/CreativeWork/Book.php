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
			return $returns;
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
		$params['additionalType'] = isset($params['additionalType']) ? "Book,".$params['additionalType'] : "Book";
		return parent::create('creativeWork',$params);
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
