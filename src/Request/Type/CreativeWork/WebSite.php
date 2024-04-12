<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

class WebSite extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('webSite');
	}

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
			return parent::array_sort($returns, $params);
		} else {
			return $data;
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$name = $params['name'] ?? null;
		$url = $params['url'] ?? null;
		$description = $params['description'] ?? null;
		$author = $params['author'] ?? null;
		unset($params['type']);
		$params['type'][] = "WebSite";
		if ($name && $description && $author && $url) {
			// SAVE CREATIVEWORK
			return parent::createWithParent('creativeWork', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, description, author and url']);
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
