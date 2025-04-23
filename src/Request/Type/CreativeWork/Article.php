<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

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
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('article');
		$getData->setLeftJoin('creativeWork','creativeWork.idcreativeWork=article.creativeWork');
		$getData->setParams($params);
		$data = $getData->render();
		if (!empty($data) && $properties) {
			foreach ($data as $key => $item) {
				$idthing = $item['thing'];
				$author = $item['author'];
				// IMAGE OBJECT
				if (in_array('image', $properties)) {
					$dataImage = parent::getProperties('imageObject', ['idHasPart' => $idthing, 'orderBy' => 'position']);
					if (isset($dataImage[0])) {
						$data[$key]['image'] = ApiFactory::response()->type('imageObject')->setData($dataImage)->ready();
					}
				}
				// AUTHOR
				if (in_array('author', $properties)) {
					$dataAuthor = parent::getProperties('person', ['idthing'=>$author]);
					if (isset($dataAuthor[0])) {
						$data[$key]['author'] = ApiFactory::response()->type('person')->setData($dataAuthor[0])->ready();
					}
				}
			}
		}
		return parent::sortData($data);
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
