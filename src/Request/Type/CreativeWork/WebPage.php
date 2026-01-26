<?php
namespace Plinct\Api\Request\Type\CreativeWork;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship;
use Plinct\Api\Request\Type\Intangible\Breadcrumb;

class WebPage extends CreativeWork
{
	/**
	 *
	 */
	public function __construct(Relationship $relationship = null)
	{
		parent::__construct($relationship);
		$this->setTable('webPage');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$properties = parent::propertiesToArray($params['properties'] ?? null);
		$isPartOf = $params['idHasPart'] ?? $params['isPartOf'] ?? null;
		$typeIsPartOf = $params['typeIsPartOf'] ?? null;
		if ($isPartOf) {
			unset($params['isPartOf']);
			$getData = new GetData('thing_has_thing');
			$getData->setLeftJoin('thing', "`thing`.idthing=`thing_has_thing`.idIsPartOf");
			$getData->setLeftJoin('webPage', "`webPage`.thing = `thing_has_thing`.idIsPartOf");
			$getData->setLeftJoin('creativeWork', "`webPage`.creativeWork = `creativeWork`.idcreativeWork");
			$getData->setWhere("`thing_has_thing`.idHasPart='$isPartOf'");
			$getData->setParams($params);
		} else {
			$getData = new GetData('webPage');
			$getData->setParams($params);
			$getData->setLeftJoin('creativeWork', "`webPage`.creativeWork = `creativeWork`.idcreativeWork");
		}
		$data = $getData->render();
		if (isset($data['error'])) {
			return $data;
		}
		if ($properties) {
			foreach ($data as $key => $item) {
				$idthing = $item['idthing'];
				// IMAGE
				if(in_array('image',$properties)) {
					$data[$key]['image'] =  parent::getProperties('imageObject', ['idHasPart' => $idthing]);
				}
				// HAS PART
				if (in_array('hasPart', $properties)) {
					$data[$key]['hasPart'] = parent::getHasPart($idthing,'WebPage',$typeIsPartOf,['properties'=>'propertyValue','orderBy'=>'position']);
				}
				// IS PART OF
				if (in_array('isPartOf', $properties)) {
					$data[$key]['isPartOf'] = parent::getIsPartOf($idthing);
				}
				// PROPERTY
				if (in_array('propertyValue', $properties)) {
					$data[$key]['identifier'] = parent::getHasPart($idthing,'WebPage','propertyValue');
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
		$url = $params['url'] ?? null;
		$alternateName = $params['alternateName'] ?? $params['alternativeHeadline'] ?? null;
		$isPartOf = $params['isPartOf'] ?? null;
		$name = $params['name'] ?? null;
		if ($url && $alternateName && $isPartOf && $name) {
			$params = $this->addBreadcrumb($params);
			// get url host
			$getCreativeWork = ApiFactory::request()->type('creativeWork')->get(['idcreativeWork'=>$isPartOf])->ready();
			if (!empty($getCreativeWork)) {
				// SAVE CREATIVEWORK
				return parent::createWithParent('creativeWork', $params);
			} else {
				return ApiFactory::response()->message()->fail()->generic(['Has part not found!']);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(['Mandatory fields: name, url, alternateName and isPartOf']);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$params = $this->addBreadcrumb($params);
		return parent::update('creativeWork',$params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		return parent::erase('creativeWork',$params);
	}

	/**
	 * @param array|null $params
	 * @return mixed
	 */
	private function addBreadcrumb(array $params = null): array
	{
		if (isset($params['url']) && (isset($params['alternativeHeadline']) || isset($params['alternateName']) || isset($params['name']))) {
			$breadcrumb = new Breadcrumb();
			$bredcrumArray = $breadcrumb->get($params);
			$breadcrumbJson = json_encode($bredcrumArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
			$params['breadcrumb'] = $breadcrumbJson;
		}
		return $params;
	}
}
