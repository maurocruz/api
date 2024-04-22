<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server\GetData;

use Plinct\Api\ApiFactory;

abstract class GetDataAbstract
{
	/**
	 * @var string
	 */
	const __LIMIT__ = '200';
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
	 * @var array
	 */
	protected array $properties;
  /**
   * @var bool|array
   */
  protected $error = false;

  /**
   *
   */
  protected function setQuery()
  {
	  $this->query = "SELECT $this->fields FROM `$this->table`";
		if (in_array('thing',$this->properties)) {
			$this->query .= " LEFT JOIN `thing` ON `thing`.`idthing`=`$this->table`.`thing`";
    }
  }

	/**
	 */
	protected function setProperties(): void
	{
		$columnsTable = ApiFactory::request()->server()->connectBd($this->table)->showColumnsName();
		$properties = [];
	  foreach ($columnsTable as $value) {
			$properties[] = $value['column_name'] ?? $value['COLUMN_NAME'] ?? null;
	  }
		$this->properties = $properties;
	}

  /**
   */
  protected function setFields()
  {
		$fieldsArray = [];
    if (isset($this->params['fields'])) {
			$fields = $this->params['fields'].',thing,type';
			unset($this->params['fields']);
			foreach (explode(',',$fields) as $field) {
				if (in_array($field, $this->properties)) {
					$fieldsArray[] = $field;
				}
			}
			// ID IS REQUIRED IF THERE IS A hasType PROPERTY
			$idname = "id$this->table";
			if (strpos($fields,$idname) === false) {
				$fieldsArray[] = $idname;
			}
      $this->fields = implode(',',$fieldsArray);
    }
  }

  /**
   *
   */
  protected function whereCondition()
  {
		$whereCondition = null;
	  foreach ($this->params as $key => $value) {
			//  WHERE WITH PARAMS
		  if ($key == 'where') {
			  $whereCondition[] = $value;
		  }
		  // ID
		  if ($key == 'id') {
			  $idname = "id$this->table";
			  $whereCondition[] = "`$idname`=$value";
		  }
		  // LIKE CONDITION
			$likeProperty = stristr($key,"like", true);
			if ($likeProperty && array_search($likeProperty, $this->properties)) {
				$valuesLike = explode(',',$value);
				$likeWhere = [];
				foreach ($valuesLike as $item) {
					$likeWhere[] = "LOWER(REPLACE(`$likeProperty`,' ','')) LIKE LOWER(REPLACE('%$item%',' ',''))";
				}
				$whereCondition[] = implode(' AND ',$likeWhere);
			}
	  }
		// PROPERTIES WITH PARAMS
	  foreach ($this->properties as $value) {
		  $propertyValue = $this->params[$value] ?? null;
		  if ($propertyValue !== null) {
			  $fieldValue = is_string($propertyValue) ? addslashes($propertyValue) : $propertyValue;
			  $whereCondition[] = "`$value`='$fieldValue'";
		  }
	  }
	  $this->query .= $whereCondition ? " WHERE " . implode(" AND ", $whereCondition) : null;
  }

	/**
	 *
	 */
	protected function finalConditions()
	{
		$groupBy = $this->params['groupBy'] ?? null;
		$orderBy = $this->params['orderBy'] ?? null;
		$ordering = $this->params['ordering'] ?? null;
		$limit = $this->params['limit'] ?? self::__LIMIT__;
		$offset = $this->params['offset'] ?? null;
    // GROUP BY
		if ($groupBy) {
	    $this->query .= " GROUP BY $groupBy";
    }
    // ORDER BY
    if ($orderBy) {
	    $this->query .= " ORDER BY $orderBy $ordering";
    }
		// LIMIT
		if ($limit != 'none' && $limit != '') {
			$this->query .= " LIMIT $limit";
			// OFFSET
			if ($offset) {
				$this->query .= " OFFSET $offset";
			}
		}
  }
}
