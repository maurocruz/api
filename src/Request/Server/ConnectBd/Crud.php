<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server\ConnectBd;

use Plinct\Api\ApiFactory;

class Crud
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
   * @param string $table
   * @return Crud
   */
  public function setTable(string $table): Crud
  {
    $this->table = $table;
    return $this;
  }

	/**
	 * @param array $params
	 */
	public function setParams(array $params): void
	{
		$columnsTable = ApiFactory::request()->server()->connectBd($this->table)->showColumnsName();
		$newParams = [];
		foreach ($columnsTable as $value) {
			$columnName = $value['column_name'] ?? $value['COLUMN_NAME'] ?? null;
			if (array_key_exists($columnName,$params)) {
				$newParams[$columnName] = $params[$columnName];
			}
		}
		$this->params = $newParams;
	}

	/**
	 * READ
	 * @param array $params
	 * @return array
	 */
  public function read(array $params): array
  {
	  $fields = $params['fields'] ?? '*';
		$where = $params['where'] ?? null;
		$groupBy = $params['groupBy'] ?? null;
		$orderBy = $params['orderBy'] ?? null;
		$ordering = $params['ordering'] ?? 'asc';
		$limit = $params['limit'] ?? null;
		$offset =	$params['offset'] ?? null;
		$args = $params['args'] ?? null;
		//
    $query = "SELECT $fields FROM `$this->table`";
    $query .= $where ? " WHERE $where" : null;
    $query .= $groupBy ? " GROUP BY $groupBy" : null;
    $query .= $orderBy ? " ORDER BY $orderBy $ordering" : null;
    $query .= $limit ? " LIMIT $limit" : null;
    $query .= $offset ? " OFFSET $offset" : null;
    $query .= ";";
    return PDOConnect::run($query, $args);
  }

  /**
   * CREATED
   * @param array $params
   * @return array
   */
  public function created(array $params): array
  {
		$this->setParams($params);
    $names = null;
    $values = null;
    $bindValues = null;
    if (empty($this->params)) {
      return [ "message" => "Record in $this->table not created because data is empty" ];
    }
    // query
    foreach ($this->params as $key => $value) {
	    $names[] = "`$key`";
      $values[] = "?";
      $bindValues[] = $value;
    }
    $columns = implode(",", $names);
    $rows = implode(",", $values);
    $query = "INSERT INTO `$this->table` ($columns) VALUES ($rows)";
		return PDOConnect::run($query, $bindValues);
  }

  /**
   * UPDATE
   * @param array $params
   * @param string $where
   * @return array
   */
  public function update(array $params, string $where): array
  {
		$this->setParams($params);
    $names = null;
    $bindValues = null;
    if (empty($this->params)) {
      return [ "message" => "No data from update in CRUD" ];
    }

    // query
    foreach ($this->params as $key => $value) {
      $names[] = "`$key`=?";
      $bindValues[] = $value;
    }
    $namesString = implode(",", $names);
    $query = "UPDATE `$this->table` SET $namesString WHERE $where;";
    return PDOConnect::run($query, $bindValues);
  }

  /**
   * DELETE
   * @param string | array $where
   * @param null $limit
   * @return array
   */
  public function erase($where, $limit = null): array
  {
		if (is_array($where)) {
			$whereArray = null;
			foreach ($where as $key => $value) {
				$whereArray[] = "`$key`='$value'";
			}
			$where = implode(" AND ",$whereArray);
		}
    $query = "DELETE FROM `$this->table` WHERE $where";
    $query .= $limit ? " LIMIT $limit" : null;
    $query .= ";";
    $run = PDOConnect::run($query);
		$rows = $run['rows'];
	  if ((int) $rows > 0) {
      return ['status'=>'success', 'message'=>"Deleted successfully. $rows rows affected."];
    } else {
		  return ['status'=>'fail', 'message'=>"$rows rows affected."];
    }
  }
}
