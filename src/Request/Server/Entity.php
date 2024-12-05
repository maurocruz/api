<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\ConnectBd;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Schema\Schema;

abstract class Entity implements HttpRequestInterface
{

	/**
	 * @var string
	 */
	protected string $table;
	/**
	 * @var array
	 */
	protected array $params = [];
  /**
   * @var string
   */
  protected string $type;
  /**
   * @var array
   */
  protected array $properties = [];
  /**
   * @var array
   */
  protected array $hasTypes = [];

	/**
	 * @param string $table
	 */
	public function setTable(string $table): void
	{
		$this->table = $table;
	}

	/**
	 * @param array $properties
	 */
	protected function setProperties(array $properties): void
	{
		$this->properties = $properties;
	}

	/**
	 * @return string
	 */
	public function getTable(): string {
		return $this->table;
	}

	/**
	 * @param array $params
	 */
	public function setParams(array $params): void
	{
		$this->params = $params;
	}

	/**
   * GET
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
    $data = $this->getData($params);
    if (isset($data['error'])) {
      return $data;
    } else {
      return (new Schema($this->table, $this->properties, $this->hasTypes))->buildSchema($params, $data);
    }
  }

	/**
	 * @param string $property
	 * @param array $params
	 * @return array|null
	 */
	protected function getProperties(string $property, array $params): ?array
	{
		$data = ApiFactory::request()->type($property)->get($params)->ready();
		return isset($data[0]) ? ApiFactory::response()->type($property)->setData($data)->ready() : null;
	}

	/**
	 * @param array $params
	 * @param $joins
	 * @return array
	 */
  protected function getData(array $params, $joins = false): array
  {
    $data = new GetData($this->table);
	  if ($joins) {
			$data->setJoins($joins);
	  }
    $data->setParams($params);
	  return $data->render();
  }

	protected function getThingFirst(string $type, array $params): ?array
	{
		$returns = [];
		$dataThing = ApiFactory::request()->type('thing')->get(['type'=>$type] + $params)->ready();
		if (!empty($dataThing)) {
			foreach ($dataThing as $key => $valueThing) {
				$idthing = $valueThing['idthing'];
				$dataTaxon = $this->getData(['thing' => $idthing] + $params);
				if (!empty($dataTaxon)) {
					$returns[$key] = $dataTaxon[0] + $valueThing;
				}
			}
		}
		return $returns;
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
  public function post(array $params = null): array
  {
		$connect = new ConnectBd($this->table);
		$data = $connect->created($params);
	  if (empty($data)) {
			$idvalue = $connect->lastInsertId();
			$idname = "id".$this->table;
			$data = ApiFactory::request()->type($this->table)->get([$idname=>$idvalue])->ready();
			return ApiFactory::response()->message()->success("Successfully created", $data);
	  } else {
		  return ApiFactory::response()->message()->fail()->generic($data);
	  }
  }

	/**
	 * @param string $parentName
	 * @param array|null $params
	 * @param array|null $uploadedFiles
	 * @return array
	 */
	protected function createWithParent(string $parentName, array $params = null, array $uploadedFiles = null): array
	{
		$params['type'] = $params['type'] ?? ucfirst($this->table);
		// SAVE PARENT
		$dataParent = ApiFactory::request()->type($parentName)->httpRequest()->setPermission()->post($params, $uploadedFiles);
		//var_dump($dataParent);
		if (isset($dataParent['status']) && $dataParent['status'] === 'success') {
			$value = $dataParent['data'][0];
			foreach ($value as $key => $val) {
				if (str_starts_with($key, 'id')) {
					$params[substr($key,2)] = $val;
				}
			}
			// SAVE CHILD
			return self::post($params);
		}
		return ApiFactory::response()->message()->fail()->generic($dataParent);
	}

	/**
	 * PUT
	 * @param array|null $params
	 * @return array
	 */
  public function put(array $params = null): array
  {
		// CONNECT
	  $connect = new ConnectBd($this->table);
		$data = $connect->update($params);
		if ($data['status'] === 'success') {
			$idname = "id$this->table";
			$idvalue = $params[$idname] ?? null;
			if ($idvalue) {
				$getData = new GetData($this->table);
				$rowUpdated = $getData->setParams([$idname => $idvalue])->render();
				$data['data'] = $rowUpdated;
			}
		}
		return $data;
  }

	/**
	 * @param string $parentName
	 * @param array|null $params
	 * @return array
	 */
	protected function update(string $parentName, array $params = null): array
	{
		$idchildName = 'id'.$this->table;
		$idchildValue = $params[$idchildName] ?? null;
		if ($idchildValue) {
			$dataChild = self::getData([$idchildName=>$idchildValue]);
			if (!empty($dataChild)) {
				$putChild = self::put($params);
				if ($putChild['status'] === 'success') {
					$idparent = $putChild['data'][0][$parentName];
					$putParent = ApiFactory::request()->type($parentName)->put(['id'.$parentName=>$idparent] + $params)->ready();
					if ($putParent['status'] === 'success') {
						return ApiFactory::response()->message()->success('CreativeWork was updated', [$putChild, $putParent]);
					} else {
						return ApiFactory::response()->message()->error()->anErrorHasOcurred($putParent);
					}
				} else {
					return ApiFactory::response()->message()->error()->anErrorHasOcurred($putChild);
				}
			} else {
				return ApiFactory::response()->message()->fail()->returnIsEmpty();
			}
		}
		return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: $idchildName"]);
	}

  /**
   * DELETE
   * @param array $params
   * @return array
   */
  public function delete(array $params): array
  {
		$connect = new ConnectBd($this->table);
		return $connect->delete($params);
  }

	/**
	 * @param string $parentName
	 * @param array|null $params
	 * @return array
	 */
	protected function erase(string $parentName, array $params = null): array
	{
		$idchildName = 'id'.$this->table;
		$idchildValue = $params[$idchildName] ?? $params[$this->table] ?? null;
		if ($idchildValue) {
			$dataChild = self::getData([$idchildName=>$idchildValue]);
			if (!empty($dataChild)) {
				$idparent = $dataChild[0][$parentName];
				return ApiFactory::request()->type($parentName)->delete(['id'.$parentName=>$idparent])->ready();
			}else {
				return ApiFactory::response()->message()->fail()->generic($params, ucfirst($this->table).' id not found');
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(["Mandatory fields: $idchildName or $this->table"]);
		}
	}

	/**
	 * @param array $array
	 * @return array
	 */
	protected function sortData(array $array): array
	{
		$new_array = array();
		foreach ($array as $key => $value) {
			ksort($value);
			$new_array[$key] = $value;
		}
		return $new_array;
	}

	/**
	 * @param string $idHasPart
	 * @param string $typeHasPart
	 * @param string $idIsPartOf
	 * @param string $typeIsPartOf
	 * @return array
	 */
	protected function createRelationShip(string $idHasPart, string $typeHasPart, string $idIsPartOf, string $typeIsPartOf): array
	{
		$connect = new ConnectBd("thing_has_thing");
		$returns = $connect->created(['idHasPart'=>$idHasPart, 'typeHasPart'=>$typeHasPart, 'idIsPartOf'=>$idIsPartOf, 'typeIsPartOf'=>$typeIsPartOf]);
		$sqlQuery = "UPDATE `thing_has_thing` SET position = position+1 WHERE idHasPart = '$idHasPart' AND typeHasPart = '$typeHasPart' AND typeIsPartOf = '$typeIsPartOf';";
		$connect->run($sqlQuery);
		return $returns;
	}
}
