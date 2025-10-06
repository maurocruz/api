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
		$this->setType('MediaObject');
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
				$idthing = $value['thing'] ?? $value['idthing'] ?? null;
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$dataHasPart = parent::getHasPart($idthing,'MediaObject', null, $params);
					if (isset($dataHasPart[0])) {
						$data[$key]['hasPart'] = ApiFactory::response()->type('mediaObject')->setData($dataHasPart)->ready();
					}
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$dataIsPartOf = parent::getIsPartOf($idthing);
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
		$idthing = $params['idthing'] ?? $params['thing'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
		$representativeOfPage = $params['representativeOfPage'] ?? null;
		$position = $params['position'] ?? null;
		$caption = $params['caption'] ?? null;
		if ($idHasPart && $typeHasPart && $idIsPartOf) {
			if ($representativeOfPage !== null) $paramsu['representativeOfPage'] = $representativeOfPage;
			if ($position !== null) $paramsu['position'] = $position;
			if ($caption !== null) $paramsu['caption'] = $caption;
			return parent::updateRelationship($idHasPart, $typeHasPart, $idIsPartOf, 'ImageObject',$paramsu ?? []);
		} elseif ($idmediaObject || $idthing) {
			return parent::update('creativeWork', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idmediaObject, mediaObject, idthing or thing"]);
		}
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idthing = $params['idthing'] ?? $params['thing'] ?? null;
		$idmediaObject = $params['idmediaObject'] ?? $params['mediaObject'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		if ($idmediaObject || $idthing && !$idHasPart) {
			$datamediaObject = self::get($idthing ? ['thing'=>$idthing] : ['idmediaObject'=>$idmediaObject]);
			if (isset($datamediaObject[0])) {
				$value = $datamediaObject[0];
				$contentUrl = $value['contentUrl'];
				$localPahth = str_replace(ApiFactory::request()->configuration()->getHost(), $_SERVER['DOCUMENT_ROOT'], $contentUrl);
				$pathinfo = pathinfo($localPahth);
				if ($value['type'] === 'ImageObject') {
					$imageMedium = $pathinfo['dirname'].'/'.$pathinfo['filename'].'_m.'.$pathinfo['extension'];
					$imageSmall = $pathinfo['dirname'].'/'.$pathinfo['filename'].'_s.'.$pathinfo['extension'];
					$imageThumbnail = $pathinfo['dirname'].'/'.$pathinfo['filename'].'_t.'.$pathinfo['extension'];
					if(file_exists($imageMedium)) unlink($imageMedium);
					if(file_exists($imageSmall)) unlink($imageSmall);
					if(file_exists($imageThumbnail)) unlink($imageThumbnail);
				} else {
					$thumbnail = $pathinfo['dirname'].'/'.$pathinfo['filename'].'_thumb.jpeg';
					if(file_exists($thumbnail)) unlink($thumbnail);
				}
				unlink(str_replace(ApiFactory::request()->configuration()->getHost(), $_SERVER['DOCUMENT_ROOT'], $contentUrl));
			}
		}
		return parent::delete($params);
	}
}
