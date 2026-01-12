<?php
namespace Plinct\Api\Request\Server\GetData;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class GetData extends GetDataAbstract
{
	/**
	 * @param $table
	 * @param bool $withThings
	 */
  public function __construct($table, bool $withThings = true)
  {
    $this->table = $table;
	  $this->setProperties($table);
		$this->withThings = $withThings;
  }

	/**
	 * @param string $fields
	 */
	public function setFields(string $fields): void
	{
		$this->fields[] = $fields;
	}

	public function setJoin(string $table, string $condition, string $alias = null, string $tableFactor = null): GetData
	{
		$this->setProperties($table);
		if ($alias) {
			$condition = str_replace("`$table`", $alias, $condition);
			$this->setJoins("$tableFactor JOIN `$table` as $alias ON $condition", $table);
		} else {
			$this->setJoins("$tableFactor JOIN `$table` ON $condition", $table);
		}
		return $this;
	}

	/**
	 * @param string $table
	 * @param string $condition
	 * @param string|null $alias
	 * @return GetData
	 */
	public function setLeftJoin(string $table, string $condition, string $alias = null): GetData
	{
		$this->setJoin($table, $condition, $alias, 'LEFT');
		return $this;
	}

	/**
	 * @param string $joins
	 * @param string $table
	 * @return GetData
	 */
	public function setJoins(string $joins, string $table): GetData
	{
		if ($table === 'thing') {
			array_unshift($this->joins, $joins);
		} else {
			$this->joins[] = $joins;
		}
		return $this;
	}

	/**
	 * @param $params
	 * @return $this
	 */
	public function setParams($params): GetData
	{
		$this->params = $params;
		return $this;
	}

	/**
	 * @param ?string $where
	 * @return $this
	 */
	public function setWhere(?string $where): GetData
	{
		if ($where) {
			$this->where[] = $where;
		}
		return $this;
	}

	public function getQuery(): string
	{
		return $this->query;
	}

  /**
   * @return array
   */
  public function render(): array
  {
	  $this->setQuery();
    return PDOConnect::run($this->getQuery());
  }
}
