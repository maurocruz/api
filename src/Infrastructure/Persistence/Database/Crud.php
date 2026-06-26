<?php
namespace Plinct\Api\Infrastructure\Persistence\Database;

use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\GetData\GetDataInfrastructure;
use ReflectionException;

class Crud extends ConnectDB
{
	protected string $table;
	protected array $params = [];

	/**
	 * CREATE
	 * 1. $params = ['name' => 'user']
	 * @param string $table
	 * @param array $params
	 * @return array
	 * @throws ReflectionException
	 */
	public function create(string $table, array $params = []): array
	{
		// SE NÃO HOUVER DADOS RETORNA FALSO
		if (empty($params)) {
			return [ 'status' => false, "message" => "Record in $this->table not created because data is empty" ];
		}

		// HIDRATA PARAMETROS
		$hidratedParams = SupportApplication::hydrateOwnProperties($table, $params);

		// CONSTROI QUERIES
		$table = lcfirst($table);
		$names = null;
		$values = null;
		$bindValues = null;
		foreach ($hidratedParams as $key => $value) {
			$names[] = "`$key`";
			$values[] = "?";
			$bindValues[] = $value;
		}
		$columns = implode(",", $names);
		$rows = implode(",", $values);

		// EXECUTA QUERY
		$query = "INSERT INTO `$table` ($columns) VALUES ($rows)";
		$responseData = parent::run($query, $bindValues);

		// RETORNA DADOS
		if (empty($responseData)) {
			// SE FOR RELACIONAMENTO RETORNA DADOS
			if (array_key_exists('idHasPart', $hidratedParams) && array_key_exists('idIsPartOf', $hidratedParams)) {
				$itemData = $this->read('thing_has_thing', ['idHasPart' => $hidratedParams['idHasPart'], 'idIsPartOf' => $hidratedParams['idIsPartOf']])->render();
			} else {
				// BUSCA ITEM CRIADO
				$lastId = parent::lastInsertId();
				$getData= $this->read($table, ["id$table" => $lastId]);
				if ($table === 'thing') $getData->setWithThings(false);
				$itemData = $getData->render();
			}
			return [ 'status' => true, "message" => "Record in $table created", "data" => $itemData['data'] ];
		} else {
			return [ 'status' => false, "message" => "Record in $table was not created", 'data' => $responseData ];
		}
	}

	/**
	 * READ
	 * 1. $params = ['id' => 1]
	 * 2. $params = ['id' => 1, 'name' => 'user']
	 * @param string $table
	 * @param array $params
	 * @return GetDataInfrastructure
	 */
	public function read(string $table, array $params = []): GetDataInfrastructure
	{
		$getData = new GetDataInfrastructure();
		$getData->setTable($table);
		if (array_key_exists('where', $params)) {
			$params['where'] = urldecode($params['where']);
		}
		$getData->setParams($params);
		return $getData;
	}

	/**
	 * UPDATE
	 * 1. $params = ['name' => 'new name']
	 * 2. $whereClause = ['id' => 1]
	 * @param string $table
	 * @param array $params
	 * @param array $whereClause
	 * @return array
	 */
	public function update(string $table, array $params, array $whereClause): array
	{
		$bindValues = [];
		$set = [];
		$where = [];
		foreach ($params as $key => $value) {
			$set[] = "`$key` = ?";
			$bindValues[] = $value;
		}
		foreach ($whereClause as $key => $value) {
			$where[] = "`$key` = ?";
			$bindValues[] = $value;
		}
		$set = implode(",", $set);
		$where = implode(" AND ", $where);
		$query = "UPDATE `$table` SET $set WHERE $where";
		$responseData = parent::run($query, $bindValues);
		if (empty($responseData)) {
			return [ 'status' => true, "message" => "Record in $table updated", 'data' => ['params'=>$params, 'where'=>$whereClause ]];
		} else {
			return [ 'status' => false, "message" => "Record in $table was not updated", 'data' => $responseData['error'] ?? '' ];
		}
	}

	/**
	 * DELETE
	 * 1. $whereClause = ['id' => 1]
	 * 2. $limit = 1
	 * @param string $table
	 * @param array $whereClause
	 * @param int $limit
	 * @return array
	 */
  public function delete(string $table, array $whereClause, int $limit = 1): array
  {
		$whereArray = null;
	  foreach ($whereClause as $key => $value) {
		  $whereArray[] = "`$key`='$value'";
	  }
	  $where = implode(" AND ",$whereArray);
	  $query = "DELETE FROM `$table` WHERE $where";
	  $query .= " LIMIT $limit";
	  $query .= ";";
	  $run = parent::run($query);
	  $rows = $run['rows'] ?? null;
	  if ((int) $rows > 0) {
		  return ['status' => true, 'message'=>"Deleted successfully. $rows rows affected."];
	  } else {
		  return ['status' => false, 'message'=>"$rows rows affected", 'data'=>$query];
	  }
  }
}
