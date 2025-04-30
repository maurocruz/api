<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

class WebPageElement extends Entity
{
	/**
	 *
	 */
	public function __construct()
	{
		$this->setTable('webPageElement');
	}

	/**
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$isPartOf = $params['isPartOf'] ?? null;
		$getData = new GetData('webPageElement');
		$getData->setParams($params);
		$getData->setLeftJoin("creativeWork","`creativeWork`.idcreativeWork=`webPageElement`.creativeWork");
		$data = $getData->render();
		if ($properties) {
			foreach ($data as $key => $item) {
				$idthing = $item['thing'];
				// IMAGE
				if (in_array('image',$properties)) {
					$dataImageObject = parent::getProperties('imageObject', ['idHasPart' => $idthing]);
					if ($dataImageObject) {
						$data[$key]['image'] = $dataImageObject;
					}
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$data[$key]['isPartOf'] = parent::getProperties('webPage', ['creativeWork' => $isPartOf])[0];
				}
				// PROPERTY VALUE
				if (in_array('propertyValue', $properties)) {
						$query = "SELECT name, value FROM thing_has_thing
                  JOIN propertyValue ON idIsPartOf=propertyValue.idpropertyValue
                  WHERE typeHasPart='WebPageElement' AND typeIsPartOf='propertyValue' AND idHasPart='$idthing';";
						$dataPropertyValue = PDOConnect::run($query);
						foreach ($dataPropertyValue as $propertyValue) {
							$data[$key]['identifier'][] = ['@type' => 'PropertyValue', 'name' => $propertyValue['name'], 'value' => $propertyValue['value']];
						}
					}
			}
		}
	  return parent::sortData($data);
  }

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$isPartOf = $params['isPartOf'] ?? null;
		$name = $params['name'] ?? null;
		if ($isPartOf && $name) {
			// get absolute url
			$getCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$isPartOf])->ready();
			if(!empty($getCreativeWork)) {
				$valueCreativeWork = $getCreativeWork[0];
				$params['url'] = $valueCreativeWork['url'].'#'.$name;
				// SAVE CREATIVEWORK
				return parent::createWithParent('creativeWork', $params);
			} else {
				return ApiFactory::response()->message()->fail()->generic(['Has part not found!']);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, isPartOf and $text']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idwebPageElement = $params['idwebPageElement'] ?? $params['webPageElement'] ?? null;
		if ($idwebPageElement) {
			$datawebPageElement = parent::getData(['idwebPageElement'=>$idwebPageElement]);
			if (!empty($datawebPageElement)) {
				$putwebPageElement = parent::put($params);
				if ($putwebPageElement['status'] === 'success') {
					$idcreativeWork = $datawebPageElement[0]['creativeWork'];
					$putCreativeWork = ApiFactory::request()->type('creativeWork')->put(['idcreativeWork'=>$idcreativeWork] + $params)->ready();
					if ($putCreativeWork['status'] === 'success') {
						return ApiFactory::response()->message()->success('MediaObject was updated', [$putwebPageElement, $putCreativeWork]);
					}
				}
			} else {
				return ApiFactory::response()->message()->fail()->returnIsEmpty();
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idwebPageElement or webPageElement"]);
		}
		return ApiFactory::response()->message()->fail()->generic();
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idwebPageElement = $params['idwebPageElement'] ?? $params['webPageElement'] ?? null;
		if ($idwebPageElement) {
			$datawebPageElement = parent::getData(['idwebPageElement'=>$idwebPageElement]);
			if (!empty($datawebPageElement)) {
				return ApiFactory::request()->type('creativeWork')->delete(['idcreativeWork'=>$datawebPageElement[0]['creativeWork']])->ready();
			} else {
				return ApiFactory::response()->message()->fail()->generic($params,'WebPageElement id not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idwebPageElement or webPageElement"]);
		}
	}
}
