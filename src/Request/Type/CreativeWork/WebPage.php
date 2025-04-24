<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;
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
		$properties = parent::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('webPage');
		$getData->setParams($params);
		$getData->setLeftJoin('creativeWork',"`webPage`.creativeWork = `creativeWork`.idcreativeWork");
		$data = $getData->render();
		if (isset($data['error'])) {
			return $data;
		}
		if ($properties) {
			foreach ($data as $key => $item) {
				$idthing = $item['idthing'];
				$idcreativeWork = $item['idcreativeWork'];
				$isPartOf = $item['isPartOf'];
				// IMAGE
				if(in_array('image',$properties)) {
					$data[$key]['image'] =  parent::getProperties('imageObject', ['idHasPart' => $idthing]);
				}
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$dataWebPageElement = ApiFactory::request()->type('webPageElement')->get(['isPartOf'=>$idcreativeWork,'properties'=>'image,propertyValue','orderBy'=>'position'])->ready();
					if (isset($dataWebPageElement[0])) {
						$data[$key]['hasPart'] = ApiFactory::response()->type('webPageElement')->setData($dataWebPageElement)->ready();
					}
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$dataWebSite = ApiFactory::request()->type('webSite')->get(['idcreativeWork'=>$isPartOf])->ready();
					if (isset($dataWebSite[0])) {
						$data[$key]['isPartOf'] = ApiFactory::response()->type('webSite')->setData($dataWebSite[0])->ready();
					}
				}
				// PROPERTY
				if (in_array('propertyValue', $properties)) {
					$sql = "SELECT name, value FROM thing_has_thing JOIN propertyValue ON idIsPartOf=propertyValue.idpropertyValue WHERE typeHasPart='WebPage' AND typeIsPartOf='propertyValue' AND idHasPart='$idthing';";
					$dataPropertyValue = PDOConnect::run($sql);
					if (isset($dataPropertyValue[0])) {
						$data[$key]['identifier'] = ApiFactory::response()->type('propertyValue')->setData($dataPropertyValue)->ready();
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
		$url = $params['url'] ?? null;
		$alternateName = $params['alternateName'] ?? $params['alternativeHeadline'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		$name = $params['name'] ?? null;
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
