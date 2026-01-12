<?php
namespace Plinct\Api\Request\Type\Intangible;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;

class PropertyValue extends Entity
{
	public function __construct()
	{
		$this->setTable('propertyValue');
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$getData = new GetData('propertyValue');
		$getData->setParams($params);
		$data = $getData->render();
		return parent::sortData($data);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		$idpropertyValue = $params['idpropertyValue'] ?? null;
		if ($idpropertyValue) {
			return parent::put($params);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idpropertyValue"]);
		}
	}

	/**
	 * @param array|null $params
	 * @param array|null $uploadfiles
	 * @return array
	 */
	public function post(array $params = null, array $uploadfiles = null): array
	{
		$typeHasPart = $params['typeHasPart'] ?? null;
		$idHasPart = $params['idHasPart'] ?? null;
		$name = $params['name'] ?? null;
		if ($typeHasPart && $idHasPart && $name) {
			$dataPropertyValue =  parent::post($params);
			if (isset($dataPropertyValue['status']) && $dataPropertyValue['status'] == 'success') {
				$idpropertyValue = $dataPropertyValue['data'][0]['idpropertyValue'];
				$returns = parent::createRelationShip($idHasPart, ucfirst($typeHasPart), $idpropertyValue, "PropertyValue");
				if (empty($returns)) {
					return $dataPropertyValue;
				} else {
					return ApiFactory::response()->message()->fail()->generic($returns);
				}
			} else {
				return ApiFactory::response()->message()->fail()->generic($dataPropertyValue);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: typeHasPart, idHasPart and name"]);
		}
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array
	{
		$idpropertyValue = $params['idpropertyValue'] ?? null;
		if ($idpropertyValue) {
			return parent::delete(['idpropertyValue'=>$idpropertyValue]);
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: idpropertyValue"]);
		}
	}
}
