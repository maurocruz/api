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
	protected array $properties = [];
  /**
   * @var ?array
   */
  protected ?array $error = null;
	/**
	 * @var bool
	 */
	private bool $hasThing = false;

	protected ?string $joins = null;

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
	 * @param string $joins
	 */
	public function setJoins(string $joins): void
	{
		$this->joins = $joins;
	}

	/**
	 * @param string $table
	 * @return void
	 */
	protected function setProperties(string $table): void
	{
		$propertiesTable = self::getColumnNames($table);
		$propertiesThing = [];
		if (!!array_search('thing',$propertiesTable)) {
			$this->hasThing = true;
			$propertiesThing = self::getColumnNames('thing');
		}
		$this->properties = array_merge($this->properties, $propertiesTable, $propertiesThing);
	}

	/**
	 * @param string $table
	 * @return array
	 */
	private function getColumnNames(string $table): array
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
		$fieldsArray = [];
    if (isset($this->params['fields'])) {
			$fields = $this->params['fields'];
			if (str_contains($fields,'count')) {
				$this->fields = $fields;
			} else {
				$fields = $fields.',thing,type';
				unset($this->params['fields']);
				foreach (explode(',', $fields) as $field) {
					if (in_array($field, $this->properties)) {
						$fieldsArray[] = $field;
					}
				}
				// ID IS REQUIRED IF THERE IS A hasType PROPERTY
				$idname = "id$this->table";
				if (!str_contains($fields, $idname)) {
					$fieldsArray[] = $idname;
				}
				$this->fields = implode(',', $fieldsArray);
			}
    }
  }

  /**
   *
   */
  protected function whereCondition(): void
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
		  if (is_string($key)) {
				$likeProperty = stristr($key,"like", true);
				if ($likeProperty && array_search($likeProperty, $this->properties)) {
					$valuesLike = explode(',', $value);
					$likeWhere = [];
					foreach ($valuesLike as $item) {
						$likeWhere[] = "LOWER(REPLACE(`$likeProperty`,' ','')) LIKE LOWER(REPLACE('%$item%',' ',''))";
					}
					$whereCondition[] = implode(' AND ', $likeWhere);
				}
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
				if ($this->isProperty($item)) {
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
