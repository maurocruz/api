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
		$idimageObject = $params['idimageObject'] ?? null;
	  unset($params['isPartOf']);
	  unset($params['idHasPart']);
		// IF IMAGE OBJECT IS PART OF
		if ($idHasPart) {
			$getData = new GetData('thing_has_thing');
			$getData->setFields('*,thing_has_thing.caption,thing_has_thing.representativeOfPage,thing_has_thing.position');
			$getData->setLeftJoin('imageObject','`thing_has_thing`.idIsPartOf=`imageObject`.thing');
			$getData->setLeftJoin('mediaObject','`mediaObject`.idmediaObject=`imageObject`.mediaObject');
			$getData->setLeftJoin('creativeWork','`creativeWork`.idcreativeWork=`mediaObject`.creativeWork');
			$getData->setLeftJoin('thing','`thing`.idthing=`imageObject`.thing');
			$getData->setParams($params + ['where'=>"`thing_has_thing`.idHasPart='$idHasPart' AND thing_has_thing.typeIsPartOf='ImageObject'"]);
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
			if ($properties) {
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$idthing = $value['thing'];
					$dataRelationship = parent::getIsPartOf($idthing, 'ImageObject');
					if(isset($dataRelationship[0])) {
						$data[$key]['isPartOf'] = ApiFactory::response()->type('creativeWork')->setData($dataRelationship)->ready();
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
		$idthing = $params['thing'] ?? $params['idthing'] ?? null;
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
		} elseif ($idimageObject || $idthing) {
			return parent::update('mediaObject',$params);
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
		return parent::erase('mediaObject', $params);
	}
}
