<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\HttpRequestInterface;

class MediaObject extends Entity implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('mediaObject');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$returns = parent::getData($params);
		return parent::sortData($returns);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$contentUrl = $params['contentUrl'] ?? null;
		$params['type'][] = "MediaObject";
		$params['uploadDate'] = date('Y-m-d H:i:s');
		if ($contentUrl) {
			// SAVE CREATIVEWORK
			return $this->createWithParent('creativeWork', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: contentUrl']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idmediaObject = $params['idmediaObject'] ?? null;
		if ($idmediaObject) {
			$dataMediaObject = parent::getData(['idmediaObject'=>$idmediaObject]);
			if (!empty($dataMediaObject)) {
				$putMediaObject = parent::put($params);
				if ($putMediaObject['status'] === 'success') {
					$idcreativeWork = $dataMediaObject[0]['creativeWork'];
					$putCreativeWork = ApiFactory::request()->type('creativeWork')->put(['idcreativeWork'=>$idcreativeWork] + $params)->ready();
					if ($putCreativeWork['status'] === 'success') {
						return ApiFactory::response()->message()->success('MediaObject was updated', [$putMediaObject, $putCreativeWork]);
					}
				}
			} else {
				return ApiFactory::response()->message()->fail()->returnIsEmpty();
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idmediaObject or mediaObject"]);
		}
		return ApiFactory::response()->message()->fail()->generic();
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idmediaObject = $params['idmediaObject'] ?? $params['mediaObject'] ?? null;
		if($idmediaObject) {
			$datamediaObject = parent::getData(['idmediaObject'=>$idmediaObject]);
			if (!empty($datamediaObject)) {
				return ApiFactory::request()->type('creativeWork')->delete(['idcreativeWork'=>$datamediaObject[0]['creativeWork']])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'MediaObject id not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idmediaObject or mediaObject"]);
		}
	}
}
