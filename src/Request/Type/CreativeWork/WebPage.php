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
		$isPartOf = $params['isPartOf'] ?? null;
		$url = $params['url'] ?? null;
		if ($isPartOf) {
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['isPartOf'=>$isPartOf])->ready();
			foreach ($dataCreativeWork as $item) {
				$idcreativeWork = $item['idcreativeWork'];
				$dataWebPage = parent::getData(['creativeWork'=>$idcreativeWork] + $params);
				$returns[] = $dataWebPage[0] + $item;
			}
		} elseif($url) {
			$dataThing = ApiFactory::request()->type('thing')->get(['type'=>'WebPage'] + $params)->ready();
			if (!empty($dataThing)) {
				foreach ($dataThing as $item) {
					$idthing = $item['idthing'];
					$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['thing'=>$idthing] + $params)->ready();
					$idcreativeWork = $dataCreativeWork[0]['idcreativeWork'];
					$item = parent::getData(['creativeWork'=>$idcreativeWork] + $params)[0];
					$item['hasPart'] = parent::getProperties('webPageElement',['isPartOf' => $idcreativeWork]);
					$item['image'] = parent::getProperties('imageObject',['isPartOf' => $idthing]);
					$item['isPartOf'] = parent::getProperties('webSite',['creativeWork'=>$isPartOf])[0];
					$returns[] = $item + $dataCreativeWork[0];
				}
			}
		}
		else {
			$dataWebPage = parent::getData($params);
			foreach ($dataWebPage as $item) {
				$idcreativeWork = $item['creativeWork'];
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$idcreativeWork])->ready();
				$idthing = $dataCreativeWork[0]['thing'];
				$item['hasPart'] = parent::getProperties('webPageElement',['isPartOf' => $idcreativeWork]);
				$item['image'] = parent::getProperties('imageObject',['isPartOf' => $idthing]);
				$item['isPartOf'] = parent::getProperties('webSite',['creativeWork'=>$isPartOf])[0];
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
		$alternateName = $params['alternateName'] ?? $params['alternativeHeadline'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		$name = $params['name'] ?? null;
		$params['type'][] = "WebPage";
		if ($url && $alternateName && $isPartOf && $name) {
			$params = $this->addBreadcrumb($params);
			// get url host
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$isPartOf])->ready();
			if (!empty($dataCreativeWork)) {
				// SAVE CREATIVEWORK
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->post($params)->ready();
				if (isset($dataCreativeWork[0])) {
					$idthing = $dataCreativeWork[0]['thing'];
					$idcreativeWork = $dataCreativeWork[0]['idcreativeWork'];
					// SAVE WEBPAGE
					$dataWebPage = parent::post(['creativeWork'=>$idcreativeWork, 'thing'=>$idthing] + $params);
					return ApiFactory::response()->type('webPage')->setData($dataWebPage)->ready();
				}
			} else {
				return ApiFactory::response()->message()->fail()->generic(['Has part not found!']);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, url, alternateName and isPartOf']);
		}
		return ApiFactory::response()->message()->fail()->generic($dataCreativeWork);
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
