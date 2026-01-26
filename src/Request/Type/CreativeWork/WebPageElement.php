<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;

class WebPageElement extends CreativeWork
{
	/**
	 *
	 */
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('webPageElement');
	}

	/**
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$idHasPart = $params['idHasPart'] ?? null;
	  $typeIsPartOf = $params['typeIsPartOf'] ?? null;
		// ID HAS PART
		if ($idHasPart) {
			$getData = new GetData('thing_has_thing',false);
			$getData->setLeftJoin('thing','`thing`.idthing=`thing_has_thing`.idIsPartOf');
			$getData->setLeftJoin('webPageElement','`webPageElement`.thing=`thing`.idthing');
			$getData->setLeftJoin("creativeWork", "`creativeWork`.idcreativeWork=`webPageElement`.creativeWork");
			$getData->setParams($params + ['typeIsPartOf'=>'WebPageElement']);
		} else {
			$getData = new GetData('webPageElement');
			$getData->setParams($params);
			$getData->setLeftJoin("creativeWork", "`creativeWork`.idcreativeWork=`webPageElement`.creativeWork");
		}
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
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$data[$key]['hasPart'] = parent::getHasPart($idthing,'WebPageElement', $typeIsPartOf, ['properties'=>'propertyValue']);
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$data[$key]['isPartOf'] = parent::getIsPartOf($idthing, 'WebPageElement',['properties'=>'isPartOf']);
				}
				// PROPERTY VALUE
				if (in_array('propertyValue', $properties)) {
					$data[$key]['identifier'] = parent::getHasPart($idthing,'WebPageElement','propertyValue');
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
		$idHasPart = $params['idHasPart'] ?? null;
		$idIsPartOf = $params['idIsPartOf'] ?? null;
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
		} elseif($idHasPart && $idIsPartOf) {
			return self::updateRelationship( $idHasPart, $idIsPartOf, $params);
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
