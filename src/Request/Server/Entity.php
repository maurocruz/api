<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\PlinctApp;
use Plinct\Api\Request\Server\ConnectBd\ConnectBd;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship\Relationship;
use Plinct\Api\Request\Server\Schema\Schema;
use Plinct\Tool\Curl;

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
   * GET
   * @param array $params
   * @return array
   */
  public function get(array $params = []): array
  {
    if (isset($params['tableHasPart']) && isset($params['idHasPart'])) {
      $data = (new Relationship($params['tableHasPart'], (int) $params['idHasPart'], $this->table))->getRelationship($params);
    } else {
      $data = $this->getData($params);
    }
    if (isset($data['error'])) {
      return $data;
    } else {
      return (new Schema($this->table, $this->properties, $this->hasTypes))->buildSchema($params, $data);
    }
  }

	public function getProperties(string $property, array $params): ?array
	{
		$data = ApiFactory::request()->type($property)->get($params)->ready();
		return isset($data[0]) ? ApiFactory::response()->type($property)->setData($data)->ready() : null;
	}


  /**
   * @param $params
   * @return array
   */
  protected function getData($params): array
  {
    // obter opção se houver subClassOf como parâmetro;
    if (isset($params['subClassOf'])) {
      $whereArray = null;
      $class = $params['subClassOf'];
      $queryStringArray = ['class'=>$class,'format'=>'hierarchyText','subClass'=>'true'];
      if($this->table == 'service') {
        $queryStringArray['source'] = 'serviceCategory';
      }
      $soloData = json_decode(Curl::getUrlContents(PlinctApp::$soloineApi . "?" . http_build_query($queryStringArray)), true);
      if (isset($soloData['@graph'])) {
        foreach ($soloData['@graph'] as $key => $value) {
          $whereArray[] = $this->table == 'service' ? "`category`='$key'" : "`additionalType`='$key'";
        }
        $params['where'] = "(" . implode(" OR ", $whereArray) . ")";
      }
    }
    $data = new GetData($this->table);
    $data->setParams($params);
    return $data->render();
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
			return ApiFactory::request()->type($this->table)->get([$idname=>$idvalue])->ready();
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
		// SAVE PARENT
		$dataParent = ApiFactory::request()->type($parentName)->httpRequest()->setPermission()->post($params, $uploadedFiles);
		if (isset($dataParent[0])) {
			$idparent = $dataParent[0]['id'.lcfirst($parentName)];
			$idthing = $dataParent[0]['idthing'] ?? $dataParent[0]['thing'];
			// SAVE CHILD
			return self::post([$parentName=>$idparent, 'thing'=>$idthing] + $params);
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
		return ApiFactory::response()->message()->fail()->inputDataIsMissing($params);
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
	 * @param array|null $params
	 * @return array
	 */
	protected function array_sort(array $array, array $params): array
	{
		$new_array = array();
		$sortable_array = array();
		$orderBy = $params['orderBy'] ?? null;
		$ordering = isset($params['ordering']) && $params['ordering'] === 'desc' ? SORT_DESC : SORT_ASC;
		if (count($array) > 0 && $orderBy) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 === $orderBy) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
			// IF ORDERBY NOT FOUND
			if (empty($sortable_array)) {
				$new_array = $array;
			} else {
				switch ($ordering) {
					case SORT_DESC:
						arsort($sortable_array);
						break;
					default:
						asort($sortable_array);
				}
				foreach ($sortable_array as $k => $v) {
					$new_array[$k] = $array[$k];
				}
			}
		} else {
			$new_array = $array;
		}
		foreach ($new_array as $key => $value) {
			ksort($value);
			$new_array[$key] = $value;
		}
		return $new_array;
	}
}
