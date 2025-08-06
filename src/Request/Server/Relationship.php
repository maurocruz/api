<?php
namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\ConnectBd;
use Plinct\Api\Request\Server\GetData\GetData;

class Relationship
{
	/**
	 * @param string $idHasPart
	 * @param string $typeHasPart
	 * @param string $idIsPartOf
	 * @param string $typeIsPartOf
	 * @return array
	 */
	public function createRelationShip(string $idHasPart, string $typeHasPart, string $idIsPartOf, string $typeIsPartOf): array
	{
		$connect = new ConnectBd("thing_has_thing");
		// INSERT ROW
		$returns = $connect->created(['idHasPart'=>$idHasPart, 'typeHasPart'=>$typeHasPart, 'idIsPartOf'=>$idIsPartOf, 'typeIsPartOf'=>$typeIsPartOf]);
		// UPDATE POSITION
		//$sqlQuery = "UPDATE `thing_has_thing` SET position = position+1 WHERE idHasPart = '$idHasPart' AND typeHasPart = '$typeHasPart' AND typeIsPartOf = '$typeIsPartOf';";
		//$connect->run($sqlQuery);
		if (empty($returns)) {
			return ApiFactory::response()->message()->success("relationship successfully created", ['idHasPart'=>$idHasPart, 'typeHasPart'=>$typeHasPart, 'idIsPartOf'=>$idIsPartOf, 'typeIsPartOf'=>$typeIsPartOf]);
		} else {
			return ApiFactory::response()->message()->fail()->generic($returns);
		}
	}
	/**
	 * @param array|null $params
	 * @return array
	 */
	public function getHasPart(array $params = null): array
	{
		$returns = [];
		$getData = new GetData('thing_has_thing');
		$getData->setParams($params);
		$data = $getData->render();
		if (isset($data[0])) {
			foreach ($data as $value) {
				$typeIsPartOf = $value['typeIsPartOf'];
				$idIsPartOf = $value['idIsPartOf'];
				$dataHasParts = ApiFactory::request()->type(lcfirst($typeIsPartOf))->get(['thing'=>$idIsPartOf])->ready();
				if (isset($dataHasParts[0])) {
					$dataHasPart = $dataHasParts[0];
					$returns[] = ApiFactory::response()->type(lcfirst($typeIsPartOf))->setData($dataHasPart)->ready();
				}
			}
		}
		return $returns;
	}

	public function getIsPartOf(array $params = null): array
	{
		$returns = [];
		$getData = new GetData('thing_has_thing');
		$getData->setParams($params);
		$data = $getData->render();
		if (isset($data[0])) {
			foreach ($data as $value) {
				$typeHasPart = $value['typeHasPart'];
				$idHasPart = $value['idHasPart'];
				$dataIsPartOf = ApiFactory::request()->type(lcfirst($typeHasPart))->get(['thing'=>$idHasPart])->ready();
				if (isset($dataIsPartOf[0])) {
					$dataIsPartOf = $dataIsPartOf[0];
					$returns[] = ApiFactory::response()->type(lcfirst($typeHasPart))->setData($dataIsPartOf)->ready();
				}
			}
		}
		return $returns;
	}
}
