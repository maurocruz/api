<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
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
		$properties = $params['properties'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		if ($isPartOf) {
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['isPartOf'=>$isPartOf])->ready();
			foreach ($dataCreativeWork as $item) {
				$idcreativeWork = $item['idcreativeWork'];
				$dataWebPage = parent::getData(['creativeWork'=>$idcreativeWork] + $params);
				$returns[] = $dataWebPage[0] + $item;
			}
		} else {
			$dataWebPage = parent::getData($params);
			foreach ($dataWebPage as $item) {
				$idcreativeWork = $item['creativeWork'];
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$idcreativeWork])->ready();
				// PROPERTIES
				if ($properties) {
					if (stripos($properties, 'hasPart') !== false) {
						$dataWebPageElement = ApiFactory::request()->type('webPageElement')->get(['isPartOf' => $idcreativeWork])->ready();
						$item['hasPart'] = ApiFactory::response()->type('webPageElement')->setData($dataWebPageElement)->ready();
					}
					if (stripos($properties,'imageObject') !== false || stripos($properties,'image') !== false) {
						$thing = $dataCreativeWork[0]['thing'];
						$dataImageObject = ApiFactory::request()->type('imageObject')->get(['isPartOf'=>$thing])->ready();
						$item['image'] = isset($dataImageObject[0]) ? ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready() : null;
					}
					if (stripos($properties,'isPartOf') !== false) {
						$isPartOf = $dataCreativeWork[0]['isPartOf'];
						$dataWebSite = ApiFactory::request()->type('webSite')->get(['creativeWork'=>$isPartOf])->ready();
						$item['isPartOf'] = ApiFactory::response()->type('webSite')->setData($dataWebSite)->ready()[0];
					}
				}
				$returns[] = $item + $dataCreativeWork[0];
			}
		}
		return parent::array_sort($returns, $params);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$url = $params['url'] ?? null;
		$alternativeHeadline = $params['alternativeHeadline'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		$name = $params['name'] ?? null;
		$params['type'][] = "WebPage";
		if ($url && $alternativeHeadline && $isPartOf && $name) {
			$params = $this->addBreadcrumb($params);
			// get url host
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$isPartOf])->ready();
			if (!empty($dataCreativeWork)) {
				// set absolut url
				$valueCreativeWork = $dataCreativeWork[0];
				$webSiteUrl = $valueCreativeWork['url'];
				$params['url'] = $webSiteUrl . $url;
				// SAVE CREATIVEWORK
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->post($params)->ready();
				if (isset($dataCreativeWork['id'])) {
					$idcreativeWork = $dataCreativeWork['id'];
					// SAVE WEBPAGE
					return parent::post(['creativeWork'=>$idcreativeWork] + $params);
				}
			} else {
				return ApiFactory::response()->message()->fail()->generic(['Has part not found!']);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, url, alternativeHeadline and isPartOf']);
		}
		return ApiFactory::response()->message()->fail()->generic($dataCreativeWork);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
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
