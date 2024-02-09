<?php

declare(strict_types=1);

namespace Plinct\Api\Server;

use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\PlinctApi;
use Plinct\Api\Server\Relationship\Relationship;
use Plinct\Api\Server\Schema\Schema;
use Plinct\PDO\Crud;
use Plinct\Tool\Curl;
use ReflectionClass;
use ReflectionException;
use Plinct\PDO\PDOConnect;

abstract class Entity extends Crud implements HttpRequestInterface
{
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
      $data = (new Relationship($params['tableHasPart'], $params['idHasPart'], $this->table))->getRelationship($params);
    } else {
      $data = $this->getData($params);
    }

    if (isset($data['error'])) {
      return $data;
    } else {
      return (new Schema($this->type, $this->properties, $this->hasTypes))->buildSchema($params, $data);
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

      $soloData = json_decode(Curl::getUrlContents(PlinctApi::$soloineApi . "?" . http_build_query($queryStringArray)), true);

      if (isset($soloData['@graph'])) {
        foreach ($soloData['@graph'] as $key => $value) {
          $whereArray[] = $this->table == 'service' ? "`category`='$key'" : "`additionalType`='$key'";
        }
        $params['where'] = "(" . implode(" OR ", $whereArray) . ")";
      }
    }

    $data = new GetData\GetData($this->table);
    $data->setParams($params);
    return $data->render();
  }

  /**
   * @param array $params
   * @return array
   */
  public function post(array $params): array
  {
    // if relationship
    if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
      $relationship = new Relationship($params['tableHasPart'], $params['idHasPart'], $this->table);
      unset($params['tableHasPart'],$params['idHasPart']);
      return $relationship->postRelationship($params);
    }

    $message = parent::created($params);
    $lastId = PDOConnect::lastInsertId();

    if ($lastId == '0') {
      return $message;
    } else {
      return [ "id" => $lastId ];
    }
  }

	/**
	 * PUT
	 * @param array|null $params
	 * @return array
	 */
  public function put(array $params = null): array
  {
    // if relationship
    if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
      $relationship = new Relationship($params['tableHasPart'], $params['idHasPart'], $this->table);
      unset($params['tableHasPart'],$params['idHasPart']);
      return $relationship->putRelationship($params) ?? [];
    }
    unset($params['tableHasPart']);

    $idName = "id".$this->table;
    $idValue = $params['id'] ?? $params['idHasPart'] ?? $params[$idName] ?? null;
    unset($params['id']);
    unset($params['idHasPart']);

    if ($idValue) {
      return parent::update($params, "`$idName`=$idValue");
    } else {
      die("No id defined in put request! (".__FILE__." in line ".__LINE__.")");
    }
  }

  /**
   * DELETE
   * @param array $params
   * @return array
   */
  public function delete(array $params): array
  {
    if (isset($params['tableHasPart']) && isset($params['idHasPart']) && isset($params['tableIsPartOf']) && isset($params['idIsPartOf'])) {
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
    }
  }

  /**
   * CREATE SQL
   * @param string|null $type
   * @return array
   * @throws ReflectionException
   */
  public function createSqlTable(string $type = null): array
  {
    $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
    $reflection = new ReflectionClass($className);
    $sqlFile = dirname($reflection->getFileName()) . "/" . ucfirst($type) . ".sql";

    // RUN SQL FILE
    if (file_exists($sqlFile)) {
      $data = PDOConnect::run(file_get_contents($sqlFile));
      if (array_key_exists("error", $data)) {
        return $data;
      }
      return [ "message" => "Sql table for ".$type. " created successfully!" ];

    } else {
      return [ "message" => "Sql table for ".$type." not created!" ];
    }
  }

  /**
   * @return array
   */
  public function getHasType(): array
  {
    return $this->hasTypes;
  }
}
