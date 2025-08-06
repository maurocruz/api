<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Exception;
use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;

class ImageObject extends MediaObject
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTable('imageObject');
	}

	/**
   * @param array $params
   * @return array
   * @throws Exception
   */
  public function get(array $params = []): array
  {
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$idHasPart = $params['idHasPart'] ?? null;
		$fields = $params['fields'] ?? null;
	  $isPartOf = $params['isPartOf'] ?? null;
		$idimageObject = $params['idimageObject'] ?? null;
	  unset($params['isPartOf']);
	  unset($params['idHasPart']);
		// IF IMAGE OBJECT IS PART OF
		if ($idHasPart) {
			$getData = new GetData('thing_has_imageObject');
			$getData->setFields('*,thing_has_imageObject.caption,thing_has_imageObject.href,thing_has_imageObject.position');
			$getData->setLeftJoin('imageObject','`thing_has_imageObject`.idimageObject=`imageObject`.idimageObject');
			$getData->setLeftJoin('mediaObject','`mediaObject`.idmediaObject=`imageObject`.mediaObject');
			$getData->setLeftJoin('creativeWork','`creativeWork`.idcreativeWork=`mediaObject`.creativeWork');
			$getData->setLeftJoin('thing','`thing`.idthing=`imageObject`.thing');
			$getData->setParams($params + ['where'=>"`thing_has_imageObject`.idthing=$idHasPart"]);
			$data = $getData->render();
		}
		// HAS PART
		else if ($isPartOf && $idimageObject) {
			$getData = new GetData('thing_has_imageObject');
			$getData->setLeftJoin('thing','`thing`.idthing=`thing_has_imageObject`.idthing');
			$getData->setParams($params);
			$data = $getData->render();
		}
		// COUNT
		else if ($fields == 'count') {
			$getData = new GetData('imageObject', false);
			$getData->setFields("count(idimageObject) as count");
			$data = $getData->render();
		} else {
			$getData = new GetData('imageObject');
			$getData->setLeftJoin('mediaObject','`mediaObject`.idmediaObject=`imageObject`.mediaObject');
			$getData->setLeftJoin('creativeWork','`creativeWork`.idcreativeWork=`mediaObject`.creativeWork');
			if ($idimageObject) $getData->setWhere("idimageObject=$idimageObject");
			$getData->setParams($params);
			$data = $getData->render();
		}
		foreach ($data as $key => $value) {
			if (isset($value['representativeOfPage'])) {
				$data[$key]['representativeOfPage'] = !!$value['representativeOfPage'];
			}
			if ($isPartOf && $idimageObject) {
				$typeHasPart = lcfirst($value['type']);
				$idHasPart = $value['idthing'];
				$dataHasPart = ApiFactory::request()->type($typeHasPart)->get(['thing'=>$idHasPart])->ready();
				if(isset($dataHasPart[0])) {
					$data[$key] = $value + $dataHasPart[0];
				}
			}
			if ($properties) {
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$dataGetHasPart = new GetData('thing_has_imageObject');
					$dataGetHasPart->setLeftJoin('thing','`thing`.idthing=`thing_has_imageObject`.idthing');
					$dataGetHasPart->setParams($params);
					$dataHasPart = $dataGetHasPart->render();
					if (isset($dataHasPart[0])) {
						foreach ($dataHasPart as $valueHasPart) {
							$typeHasPart = lcfirst($valueHasPart['type']);
							$dataHasPart = ApiFactory::request()->type($typeHasPart)->get(['thing'=>$valueHasPart['idthing']])->ready();
							if(isset($dataHasPart[0])) {
								$data[$key]['isPartOf'][] = ApiFactory::response()->type($typeHasPart)->setData($dataHasPart[0])->ready();
							}
						}
					}
				}
			}
		}
	  return parent::sortData($data);
  }

	/**
	 * @throws Exception
	 */
	public function post(array $params = null, ?array $uploadfiles = null): array
	{
		$params['type'] = 'imageObject';
		return parent::post($params, $uploadfiles);
	}

	/**
	 * @param ?array $params
	 * @return array
	 * @throws Exception
	 */
  public function put(array $params = null): array
  {
		$idimageObject = $params['idimageObject'] ?? null;
		if ($idimageObject) {
			$dataImageObject = self::get(['idimageObject'=>$idimageObject]);
			if (isset($dataImageObject[0])) {
				$idmediaObject = $dataImageObject[0]['mediaObject'];
				$params['idmediaObject'] = $idmediaObject;
				return parent::put($params);
			} else {
				return ApiFactory::response()->message()->fail()->generic(["ImageObject is not found"]);
			}
		} else {
			return ApiFactory::response()->message()->fail()->generic(["Mandatory not found: idimageObject"]);
		}
  }

	/**
	 * @param array $params
	 * @return array
	 * @throws Exception
	 */
	public function delete(array $params): array
	{
		$idimageObject = $params['idimageObject'] ?? $params['imageObject'] ?? null;
		if ($idimageObject) {
			$dataImageObject = self::get(['idimageObject'=>$idimageObject]);
			if (isset($dataImageObject[0])) {
				$idmediaObject = $dataImageObject[0]['mediaObject'];
				$params['idmediaObject'] = $idmediaObject;
				return parent::delete($params);
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'ImageObject is not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idimageObject or imageObject!"]);
		}
	}
}
