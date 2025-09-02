<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\HttpRequestInterface;

class MediaObject extends CreativeWork implements HttpRequestInterface
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTable('mediaObject');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$getData = new GetData('mediaObject');
		$getData->setLeftJoin('creativeWork','creativeWork.idcreativeWork=mediaObject.creativeWork');
		$getData->setParams($params);
		$data = $getData->render();
		if (isset($data[0]['idmediaObject'])) {
			foreach ($data as $key => $value) {
				$idmediaObject = $value['idmediaObject'];
				$idthing = $value['thing'];
				$type = $value['type'];
				// ????????
				if ($type !== 'MediaObject' && $type !== 'Thing') {
					$getDataMediaObject = new GetData(lcfirst($type));
					$getDataMediaObject->setParams(['mediaObject'=>$idmediaObject]);
					$dataMediaObject = $getDataMediaObject->render();
					if (isset($dataMediaObject[0])) {
						$data[$key] = $dataMediaObject[0] + $value;
					}
				}
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$dataHasPart = parent::getHasPart($idthing,'MediaObject');
					if (isset($dataHasPart[0])) {
						$data[$key]['hasPart'] = ApiFactory::response()->type('mediaObject')->setData($dataHasPart)->ready();
					}
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$dataIsPartOf = parent::getIsPartOf($idthing, 'MediaObject');
					if (isset($dataIsPartOf[0])) {
						$data[$key]['isPartOf'] = ApiFactory::response()->type('creativeWork')->setData($dataIsPartOf)->ready();
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
	 * @throws Exception
	 */
	public function post(array $params = null, ?array $uploadfiles = null): array
	{
		$idHasPart = $params['idHasPart'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		$typeIsPartOf = $params['typeIsPartOf'] ?? null;
		if (!empty($uploadfiles)) {
			$params['typeHasPart'] = $params['typeHasPart'] ?? 'MediaObject';
			return parent::uploadfiles($params, $uploadfiles);
		} elseif ($idHasPart && $typeHasPart && $idIsPartOf && $typeIsPartOf) {
			return self::createRelationShip($idHasPart, $typeHasPart, $idIsPartOf, $typeIsPartOf, $params);
		} else {
			$contentUrl = $params['contentUrl'] ?? null;
			$params['uploadDate'] = $params['uploadDate'] ?? date('Y-m-d H:i:s');
			if ($contentUrl) {
				return $this->createWithParent('creativeWork', $params);
			} else {
				return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: contentUrl']);
			}
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
			$dataMediaObject = self::get(['idmediaObject'=>$idmediaObject]);
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
		$idHasPart = $params['idHasPart'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		$typeIsPartOf = $params['typeIsPartOf'] ?? null;
		if($idmediaObject && !$idHasPart) {
			$datamediaObject = self::get(['idmediaObject'=>$idmediaObject]);
			if (isset($datamediaObject[0])) {
				$value = $datamediaObject[0];
				$idthing = $value['thing'];
				$contentUrl = $value['contentUrl'];
				if ($value['type'] === 'ImageObject') {
					$localPahth = str_replace(ApiFactory::request()->configuration()->getHost(), $_SERVER['DOCUMENT_ROOT'], $contentUrl);
					$pathinfo = pathinfo($localPahth);
					$imageMedium = $pathinfo['dirname'].'/'.$pathinfo['filename'].'_m.'.$pathinfo['extension'];
					$imageSmall = $pathinfo['dirname'].'/'.$pathinfo['filename'].'_s.'.$pathinfo['extension'];
					$imageThumbnail = $pathinfo['dirname'].'/'.$pathinfo['filename'].'_t.'.$pathinfo['extension'];
					if(file_exists($imageMedium)) unlink($imageMedium);
					if(file_exists($imageSmall)) unlink($imageSmall);
					if(file_exists($imageThumbnail)) unlink($imageThumbnail);
				}
				unlink(str_replace(ApiFactory::request()->configuration()->getHost(), $_SERVER['DOCUMENT_ROOT'], $contentUrl));
				return ApiFactory::request()->type('thing')->delete(['idthing'=>$idthing])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'MediaObject id not found');
			}
		} elseif ($idHasPart && $typeHasPart && $idIsPartOf) {
			return parent::deleteRelationship($idHasPart, $typeHasPart, $idIsPartOf, $typeIsPartOf);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idmediaObject or mediaObject"]);
		}
	}
}
