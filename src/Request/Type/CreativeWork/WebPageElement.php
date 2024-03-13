<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;

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
		$returns = [];
		$properties = $params['properties'] ?? null;
	  $isPartOf = $params['isPartOf'] ?? null;
	  if ($isPartOf) {
		  $dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['isPartOf'=>$isPartOf])->ready();
		  foreach ($dataCreativeWork as $item) {
			  $idcreativeWork = $item['idcreativeWork'];
			  $dataWebPage = parent::getData(['creativeWork'=>$idcreativeWork] + $params);
			  $returns[] = $dataWebPage[0] + $item;
		  }
	  } else {
		  $data = parent::getData($params);
		  foreach ($data as $value) {
			  $idcreativeWork = $value['creativeWork'];
			  $dataCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork' => $idcreativeWork])->ready();
				// PROPERTIES
			  if ($properties) {
				  if (stripos($properties, 'image') !== false) {
						$idthing = $dataCreativeWork[0]['thing'];
					  $dataImageObject = ApiFactory::request()->type('imageObject')->get(['isPartOf' => $idthing])->ready();
					  $value['image'] = ApiFactory::response()->type('imageObject')->setData($dataImageObject)->ready();
				  }
			  }
			  $returns[] = $value + $dataCreativeWork[0];
		  }
	  }
	  return parent::array_sort($returns, $params);
  }



	public function post(array $params = null): array
	{
		$isPartOf = $params['isPartOf'] ?? null;
		$text = $params['text'] ?? null;
		$name = $params['name'] ?? null;
		$params['additionalType'] = "WebPageElement";
		if ($isPartOf && $text && $name) {
			return parent::create('creativeWork', $params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, isPartOf and $text']);
		}
	}

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
