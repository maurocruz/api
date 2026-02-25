<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;

class WebSite extends CreativeWork
{
	/**
	 *
	 */
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('webSite');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('webSite');
		$getData->setLeftJoin('creativeWork','creativeWork.idcreativeWork=webSite.creativeWork');
		$getData->setParams($params);
		$data = $getData->render();
		if (!empty($data)) {
			foreach ($data as $key => $item) {
				$idthing = $item['thing'] ?? null;
				// PROPERTIES
				if ($properties) {
					if (in_array('hasPart', $properties)) {
						$data[$key]['hasPart'] = parent::getHasPart($idthing,'WebSite');
					}
				}
			}
		}
		return parent::sortData($data);
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
