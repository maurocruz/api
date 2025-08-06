<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;

class WebSite extends CreativeWork
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTable('webSite');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = [];
		$properties = $params['properties'] ?? null;
		$data = parent::getData($params);
		if (!empty($data)) {
			foreach ($data as $item) {
				// CREATIVE WORK
				$idcreativeWork = $item['creativeWork'];
				$dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$idcreativeWork])->ready();
				// PROPERTIES
				if ($properties) {
					if (stripos($properties, 'hasPart') !== false) {
						$dataWebPage = ApiFactory::request()->type('webPage')->get(['isPartOf' => $idcreativeWork])->ready();
						$item['hasPart'] = ApiFactory::response()->type('webPage')->setData($dataWebPage)->ready();
					}
				}
				// RESPONSE
				if (isset($dataCreativeWork[0])) {
					$returns[] = $item + $dataCreativeWork[0];
				} else {
					$returns[] = $item;
				}
			}
			return parent::sortData($returns);
		} else {
			return $data;
		}
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		$name = $params['name'] ?? null;
		$url = $params['url'] ?? null;
		unset($params['type']);
		if ($name &&  $url) {
			// SAVE CREATIVEWORK
			return parent::createWithParent('creativeWork', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name and url']);
		}
	}

	public function put(array $params = null): array
	{
		$idwebSite = $params['idwebSite'] ?? $params['webSite'] ?? null;
		if ($idwebSite) {
			$dataWebSite = parent::getData(['idwebSite'=>$idwebSite]);
			if (!empty($dataWebSite)) {
				$putWebSite = parent::put($params);
				if ($putWebSite['status'] === 'success') {
					$idcreativeWork = $dataWebSite[0]['creativeWork'];
					$putCreativeWork = ApiFactory::request()->type('creativeWork')->put(['idcreativeWork'=>$idcreativeWork] + $params)->ready();
					if ($putCreativeWork['status'] === 'success') {
						return ApiFactory::response()->message()->success('WebSite was updated', [$putWebSite, $putCreativeWork]);
					}
				}
			} else {
				return ApiFactory::response()->message()->fail()->returnIsEmpty();
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idwebSite or webSite"]);
		}
		return ApiFactory::response()->message()->fail()->generic();
	}

	public function delete(array $params): array
	{
		$idwebSite = $params['idwebSite'] ?? $params['webSite'] ?? null;
		if ($idwebSite) {
			$dataWebSite = parent::getData(['idwebSite'=>$idwebSite]);
			if (!empty($dataWebSite)) {
				return ApiFactory::request()->type('creativeWork')->delete(['idcreativeWork'=>$dataWebSite[0]['creativeWork']])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'WebSite id not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idwebSite or webSite"]);
		}
	}
}
