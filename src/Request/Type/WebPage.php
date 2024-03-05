<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

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
		if ($isPartOf) {
			$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['isPartOf'=>$isPartOf])->ready();
			foreach ($dataCreativeWork as $item) {
				$idcreativeWork = $item['idcreativeWork'];
				$dataWebPage = parent::getData(['creativeWork'=>$idcreativeWork] + $params);
				unset($dataWebPage[0]['creativeWork']);
				$returns[] = $dataWebPage[0] + $item;
			}
		} else {
			$dataWebPage = parent::getData($params);
			foreach ($dataWebPage as $item) {
				$idcreativeWork = $item['creativeWork'];
				unset($item['creativeWork']);
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$idcreativeWork])->ready();
				$returns[] = $item + $dataCreativeWork[0];
			}
		}
		return $returns;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function post(array $params): array
	{
		$url = $params['url'] ?? null;
		$alternativeHeadline = $params['alternativeHeadline'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		if ($url && $alternativeHeadline && $isPartOf) {
			$params = $this->addBreadcrumb($params);
			// SAVE THING
			$dataThing = ApiFactory::request()->type('thing')->post(['type'=>'webPage'] + $params)->ready();
			if (isset($dataThing['id'])) {
				$idthing = $dataThing['id'];
				// SAVE CREATIVEWORK
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->post(['thing'=>$idthing] + $params)->ready();
				if (isset($dataCreativeWork['id'])) {
					$idcreativeWork = $dataCreativeWork['id'];
					// SAVE WEBPAGE
					$dataWebpAGE = parent::post(['creativeWork'=>$idcreativeWork] + $params);
					// RETURN
					return ApiFactory::response()->message()->success('WebPage was created', $dataWebpAGE);
				}
			};
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: url, alternativeHeadline and isPartOf']);
		}
		return ApiFactory::response()->message()->fail()->generic();
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return parent::put($this->addBreadcrumb($params));
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
