<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Type\Intangible\Breadcrumb;

class WebPage extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('webPage');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = [];
		$isPartOf = $params['isPartOf'] ?? null;
		$url = $params['url'] ?? null;
		$properties = $params['properties'] ?? null;
		if ($isPartOf) {
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['isPartOf'=>$isPartOf])->ready();
			foreach ($dataCreativeWork as $item) {
				$idcreativeWork = $item['idcreativeWork'];
				$dataWebPage = parent::getData(['creativeWork'=>$idcreativeWork] + $params);
				$returns[] = $dataWebPage[0] + $item;
			}
		} elseif ($url) {
			$data = PDOConnect::run("SELECT * FROM webPage
LEFT JOIN creativeWork ON creativeWork.idcreativeWork=webPage.creativeWork
LEFT JOIN thing ON creativeWork.thing=thing.idthing
WHERE thing.url='$url';");

			if (!empty($data)) {
				foreach ($data as $item) {
					$idthing = $item['idthing'];
					$idcreativeWork = $item['creativeWork'];

					if ($properties) {
						if (strpos($properties, 'hasPart') !== false) $item['hasPart'] = parent::getProperties('webPageElement', ['isPartOf' => $idcreativeWork]);
						if (strpos($properties, 'image') !== false) $item['image'] = parent::getProperties('imageObject', ['isPartOf' => $idthing, 'orderBy'=>'position']);
						if (strpos($properties, 'isPartOf') !== false) $item['isPartOf'] = parent::getProperties('webSite', ['creativeWork' => $isPartOf])[0];
					}

					$returns[] = $item;
				}
			}
		} elseif (!array_key_exists('url', $params)) {
			$dataWebPage = parent::getData($params);
			foreach ($dataWebPage as $item) {
				$idcreativeWork = $item['creativeWork'];
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$idcreativeWork])->ready();
				$idthing = $dataCreativeWork[0]['thing'];

				if ($properties) {
					if (strpos($properties, 'hasPart') !== false) $item['hasPart'] = parent::getProperties('webPageElement', ['isPartOf' => $idcreativeWork]);
					if (strpos($properties, 'image') !== false) $item['image'] = parent::getProperties('imageObject', ['isPartOf' => $idthing]);
					if (strpos($properties, 'isPartOf') !== false) $item['isPartOf'] = parent::getProperties('webSite', ['creativeWork' => $isPartOf])[0];
				}

				$returns[] = $item + $dataCreativeWork[0];
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
		$url = $params['url'] ?? null;
		$alternateName = $params['alternateName'] ?? $params['alternativeHeadline'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		$name = $params['name'] ?? null;
		$params['type'][] = "WebPage";
		if ($url && $alternateName && $isPartOf && $name) {
			$params = $this->addBreadcrumb($params);
			// get url host
			$getCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$isPartOf])->ready();
			if (!empty($getCreativeWork)) {
				// SAVE CREATIVEWORK
				return parent::createWithParent('creativeWork', $params);
			} else {
				return ApiFactory::response()->message()->fail()->generic(['Has part not found!']);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, url, alternateName and isPartOf']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$params = $this->addBreadcrumb($params);
		return parent::update('creativeWork',$params);
	}

	public function delete(array $params): array
	{
		$idwebPage = $params['idwebPage'] ?? $params['webPage'] ?? null;
		if ($idwebPage) {
			$datawebPage = parent::getData(['idwebPage'=>$idwebPage]);
			if (!empty($datawebPage)) {
				return ApiFactory::request()->type('creativeWork')->delete(['idcreativeWork'=>$datawebPage[0]['creativeWork']])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'WebPage id not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idwebPage or webPage"]);
		}
	}

	/**
	 * @param array|null $params
	 * @return mixed
	 */
	private function addBreadcrumb(array $params = null): array {
		$breadcrumb = new Breadcrumb();
		$bredcrumArray = $breadcrumb->get($params);
		$breadcrumbJson = json_encode($bredcrumArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		$params['breadcrumb'] = $breadcrumbJson;
		return $params;
	}
}
