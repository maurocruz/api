<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\PlinctApp;
use Plinct\Api\Request\Server\ConnectBd\ConnectBd;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
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
		  return ['id' => $connect->lastInsertId()];
	  } else {
		  return ApiFactory::response()->message()->fail()->generic($data);
	  }
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
   * DELETE
   * @param array $params
   * @return array
   */
  public function delete(array $params): array
  {
		$connect = new ConnectBd($this->table);
		return $connect->delete($params);
    /*if (isset($params['tableHasPart']) && isset($params['idHasPart']) && isset($params['tableIsPartOf']) && isset($params['idIsPartOf'])) {
      $relationship = new Relationship($params['tableHasPart'], $params['idHasPart'], $this->table);
      unset($params['tableHasPart'],$params['idHasPart']);
      return $relationship->deleteRelationship($params);
    } else {
      $params = array_filter($params);
      $whereArray = [];
      foreach ($params as $key => $value) {
        $whereArray[] = "`$key`='$value'";
      }
      $where = implode(" AND ", $whereArray);
      return PDOConnect::run("DELETE FROM `$this->table` WHERE $where");
    }*/
  }
}
