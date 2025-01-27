<?php
namespace Plinct\Api\Request\Server\GetData;

use Plinct\Api\ApiFactory;

abstract class GetDataAbstract
{
	/**
	 * @var string
	 */
	const __LIMIT__ = '200';
  /**
   * @var ?string
   */
  protected ?string $query = null;
  /**
   * @var string
   */
  protected string $fields = '*';
  /**
   * @var string
   */
  protected string $table;
	/**
	 * @var string|null
	 */
	protected ?string $joins = null;
  /**
   * @var array
   */
  protected array $params = [];
	/**
	 * @var array
	 */
	protected array $where = [];
	/**
	 * @var array
	 */
	protected array $properties = [];
  /**
   * @var ?array
   */
  protected ?array $error = null;
	/**
	 * @var bool
	 */
	private bool $hasThing = false;

	/**
	 * @return void
	 */
  protected function setQuery(): void
  {
	  $this->query = "SELECT $this->fields FROM `$this->table`";
	  if ($this->hasThing) {
		  $this->query .= " LEFT JOIN `thing` ON `thing`.idthing = `$this->table`.thing ";
	  }
  }

	/**
	 * @param string $table
	 * @param bool $withThings
	 * @return void
	 */
	protected function setProperties(string $table, bool $withThings): void
	{
		$propertiesTable = self::getColumnNames($table);
		$propertiesThing = [];
		if (!!array_search('thing',$propertiesTable)) {
			$this->hasThing = $withThings;
			$propertiesThing = self::getColumnNames('thing');
		}
		$this->properties = array_merge($this->properties, $propertiesTable, $propertiesThing);
	}

	/**
	 * @param string $table
	 * @return array
	 */
	protected function getColumnNames(string $table): array
	{
		$columnsTable = ApiFactory::request()->server()->connectBd($table)->showColumnsName();
		$properties = [];
		foreach ($columnsTable as $value) {
			$properties[] = $value['column_name'] ?? $value['COLUMN_NAME'] ?? null;
		}
		return $properties;
	}

	/**
	 * @param string $property
	 * @return bool
	 */
	private function isProperty(string $property): bool
	{
		return in_array($property, $this->properties);
	}

  /**
   */
  protected function setFields(): void
  {
	  $fields = $this->params['fields'] ?? null;
		if ($fields !== null) {
			$fieldsArray = array_merge(explode(',', $fields), array_filter($this->properties, function ($value) {
				if (substr($value, 0, 2) === 'id') {
					return $value;
				}
				return null;
			}));
			if(in_array('idthing', $fieldsArray)) {
				$fieldsArray[] = 'dateCreated';
				$fieldsArray[] = 'dateModified';
			}
			$this->fields = implode(',',$fieldsArray);
		}
  }

  /**
   *
   */
  protected function whereCondition(): void
  {
	  foreach ($this->params as $key => $value) {
			//  WHERE WITH PARAMS
		  if ($key == 'where') {
			  $this->where[] = $value;
		  }
		  // ID
		  if ($key == 'id') {
			  $idname = "id$this->table";
			  $this->where[] = "`$idname`=$value";
		  }
		  // LIKE CONDITION
		  if (is_string($key)) {
				$likeProperty = stristr($key,"like", true);
				if ($likeProperty && array_search($likeProperty, $this->properties)) {
					$valuesLike = explode(',', $value);
					$likeWhere = [];
					foreach ($valuesLike as $item) {
						$likeWhere[] = "LOWER(REPLACE(`$likeProperty`,' ','')) LIKE LOWER(REPLACE('%$item%',' ',''))";
					}
					$this->where[] = implode(' AND ', $likeWhere);
				}
			}
	  }
		// PROPERTIES WITH PARAMS
	  foreach ($this->properties as $value) {
		  $propertyValue = $this->params[$value] ?? null;
		  if ($propertyValue !== null) {
			  $fieldValue = is_string($propertyValue) ? addslashes($propertyValue) : $propertyValue;


				if (str_contains($fieldValue,'|')) {
					foreach (explode('|', $fieldValue) as $orValue) {
						$orArray[] = "`{$this->table}`.`$value`='$orValue'";
					}
					$this->where[] = "(" . implode(" OR ", $orArray) . ")";
				}	elseif (($this->table == 'thing') === ($value == 'idthing')) {
					$this->where[] = "`{$this->table}`.`$value`='$fieldValue'";
				} elseif (in_array($value, $this->properties) && ($value == 'idthing')) {
					$this->where[] = "`thing`.`idthing`='$fieldValue'";
				}
		  }
	  }
	  $this->query .= !empty($this->where) ? " WHERE " . implode(" AND ", array_filter($this->where)) : null;
  }

	/**
	 *
	 */
	protected function finalConditions(): void
	{
		$groupBy = $this->params['groupBy'] ?? null;
		$orderBy = $this->params['orderBy'] ?? null;
		$ordering = $this->params['ordering'] ?? null;
		$limit = $this->params['limit'] ?? self::__LIMIT__;
		$offset = $this->params['offset'] ?? null;
    // GROUP BY
		if ($groupBy && $this->isProperty($groupBy)) {
	    $this->query .= " GROUP BY $groupBy";
    }
    // ORDER BY
		if ($orderBy) {
			$orderByArray = [];
			foreach (explode(',', $orderBy) as $value) {
				$item = str_replace([' desc', ' asc'], '', trim($value));
				if ($this->isProperty($item) || $item == 'rand()') {
					$orderByArray[] = trim($value);
				}
			}
			$orderFiltered = implode(',', $orderByArray);
			if (!!$orderFiltered) {
				$this->query .= " ORDER BY $orderFiltered $ordering";
			}
		}
		// LIMIT
		if ($limit != 'none' && $limit != '' && !str_contains($this->fields,'count')) {
			$this->query .= " LIMIT $limit";
			// OFFSET
			if ($offset) {
				$this->query .= " OFFSET $offset";
			}
		}
  }
}
