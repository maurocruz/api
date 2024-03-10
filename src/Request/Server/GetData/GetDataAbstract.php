<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server\GetData;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

abstract class GetDataAbstract
{
  /**
   * @var string
   */
  protected string $query;
  /**
   * @var string
   */
  protected string $fields = '*';
  /**
   * @var string
   */
  protected string $table;
  /**
   * @var array
   */
  protected array $params = [];
  /**
   * @var bool|array
   */
  protected $error = false;
  /**
   * @var string
   */
  protected string $limit = '200';

  /**
   *
   */
  protected function setQuery() {
    $this->query = "SELECT $this->fields FROM `$this->table`";
  }

  /**
   * @param mixed $params
   */
  public function setParams($params): GetDataAbstract
  {
	  $columnsTable = ApiFactory::request()->server()->connectBd($this->table)->showColumnsName();
	  $newParams = [];
	  foreach ($columnsTable as $value) {
		  $columnName = $value['COLUMN_NAME'];
		  if (array_key_exists($columnName,$params)) {
			  $newParams[$columnName] = $params[$columnName];
		  }
	  }
    if (isset($params['limit'])) {
      $this->limit = (string) $params['limit'];
      unset($params['limit']);
		}
	  $this->params = $newParams;
		return $this;
  }

  /**
   */
  protected function setFields()
  {
    if (isset($this->params['fields'])) {
			$fields = $this->params['fields'];
			unset($this->params['fields']);
			// ID IS REQUIRED IF THERE IS A hasType PROPERTY
			$idname = "id$this->table";
			if (strpos($fields,$idname) === false) {
				$fields .= ",$idname";
			}
      $this->fields = $fields;
    }
  }

  /**
   *
   */
  protected function parseParams()
  {
    $groupBy = $this->params['groupBy'] ?? null;
    $orderBy = $this->params['orderBy'] ?? null;
    $ordering = $this->params['ordering'] ?? null;
    // GROUP BY
    if ($groupBy) $this->query .= " GROUP BY $groupBy";
    // ORDER BY
    if ($orderBy) $this->query .= " ORDER BY $orderBy $ordering";
  }

  /**
   *
   */
  protected function parseWhereClause()
  {
    $where = null;

    foreach ($this->params as $key => $value) {
      // WHERE
      if ($key == 'where') $where[] = $value;
      // ID
      if ($key == 'id') {
        $idname = "id$this->table";
        $where[] = "`$idname`=$value";
      }
      // LIKE
      $like = stristr($key,"like", true);
      if ($like) {
				$valuesLike = explode(',',$value);
				$likeWhere = [];
				foreach ($valuesLike as $item) {
					$likeWhere[] = "LOWER(REPLACE(`$like`,' ','')) LIKE LOWER(REPLACE('%$item%',' ',''))";
				}
				$where[] = implode(' AND ',$likeWhere);
      }
    }
    //
    $columnsTable = PDOConnect::run("SHOW COLUMNS FROM `$this->table`;");
    if (isset($columnsTable['error'])) {
      $this->error = $columnsTable;
    } else {
      foreach ($columnsTable as $value) {
        $field = $value['Field'];
        $valueField = $this->params[$field] ?? null;
        if ($valueField !== null) {
          $fieldValue = is_string($valueField) ? addslashes($valueField) : $valueField;
          $where[] = "`$field`='$fieldValue'";
        }
      }
      $this->query .= $where ? " WHERE " . implode(" AND ", $where) : null;
    }
  }
}
