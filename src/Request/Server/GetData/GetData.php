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
		if ($withThings) {
			foreach ($this->properties[$table] as $property) {
				if ($property === 'thing') {
					$this->setLeftJoin('thing',"`thing`.idthing = `$this->table`.thing");
				}
			}
		}
  }

	/**
	 * @param string $fields
	 */
	public function setFields(string $fields): void
	{
		$this->fields[] = $fields;
	}

	/**
	 * @param string $table
	 * @param string $condition
	 * @return void
	 */
	public function setLeftJoin(string $table, string $condition): void
	{
		$this->setProperties($table);
		$this->setJoins("LEFT JOIN `$table` ON $condition");
	}

	/**
	 * @param ?string $joins
	 * @return GetData
	 */
	public function setJoins(?string $joins): GetData
	{
		$this->joins[] = $joins;
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
		$this->setQuery();
		return $this->query;
	}

  /**
   * @return array
   */
  public function render(): array
  {
    return PDOConnect::run($this->getQuery());
  }
}
