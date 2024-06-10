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
		$about = $params['about'] ?? null;
		$properties = $params['properties'] ?? null;
		if ($about) {
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['about'=>$about] + $params)->ready();
			if (!empty($dataCreativeWork)) {
				foreach ($dataCreativeWork as $valueCreativeWork) {
					$idcreativeWork = $valueCreativeWork['idcreativeWork'];
					$dataArticle = parent::get(['creativeWork'=>$idcreativeWork] + $params);
					if(!empty($dataArticle)) {
						$returns[] = $dataArticle[0] + $valueCreativeWork;
					}
				}
			}
		} else {
			$dataArticle = parent::getData($params);
			if (!empty($dataArticle)) {
				foreach ($dataArticle as $value) {
					// CREATIVE WORK
					$idcreativeWork = $value['creativeWork'];
					$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork' => $idcreativeWork] + $params)->ready();
					// RESPONSE
					if (isset($dataCreativeWork[0])) {
						$returns[] = $value + $dataCreativeWork[0];
					} else {
						$returns[] = $value;
					}
				}
			} else {
				$returns = $dataArticle;
			}
		}
		// PROPERTIES
		if ($properties) {
			$data = $returns;
			$returns = [];
			foreach($data as $item) {
				$idthing = $item['thing'];
				if (strpos($properties, 'image') !== false) $item['image'] = parent::getProperties('imageObject', ['isPartOf' => $idthing, 'orderBy' => 'position']);
				$returns[] = $item;
			}
		}
		return parent::sortData($returns);
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
