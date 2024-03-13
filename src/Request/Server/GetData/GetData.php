<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server\GetData;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class GetData extends GetDataAbstract
{
  /**
   * @param $table
   */
  public function __construct($table)
  {
    $this->table = $table;
	  $this->setProperties();
  }

	/**
	 * @param mixed $params
	 */
	public function setParams($params): GetDataAbstract
	{
		$this->params = $params;
		return $this;
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
		//
    if($this->error) {
        return $this->error;
    }
    // PARAMS
    if ($this->params) {
	    // WHERE
	    $this->whereCondition();
      $this->finalConditions();
    }
    $this->query .= ";";
    return PDOConnect::run($this->query);
  }
}
