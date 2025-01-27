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
	  $this->setProperties($table, $withThings);
  }

	public function setLeftJoin(string $table, string $condition)
	{
		$this->properties = array_merge($this->properties,parent::getColumnNames($table));
		$this->setJoins("left join `$table` on $condition");
	}
	/**
	 * @param ?string $joins
	 * @return GetData
	 */
	public function setJoins(?string $joins): GetData
	{
		$this->joins = $joins;
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
		// FIELDS
		$this->setFields();
		// QUERY
		$this->setQuery();
		// JOIN
		if ($this->joins) {
			$this->query .= " ".$this->joins;
		}
		// WHERE
		$this->whereCondition();
		// PARAMS
		if ($this->params) {
			$this->finalConditions();
		}
		$this->query .= ";";
		return $this->query;
	}

  /**
   * @return array
   */
  public function render(): array
  {
    // FIELDS
    $this->setFields();
    // QUERY
    $this->setQuery();
		// JOIN
	  if ($this->joins) {
		  $this->query .= " ".$this->joins;
	  }
		// ERROR
    if($this->error) {
        return $this->error;
    }
	  // WHERE
	  $this->whereCondition();
    // PARAMS
    if ($this->params) {
      $this->finalConditions();
    }
    $this->query .= ";";
    return PDOConnect::run($this->query);
  }
}
