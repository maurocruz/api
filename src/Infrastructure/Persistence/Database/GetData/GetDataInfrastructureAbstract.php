<?php
namespace Plinct\Api\Infrastructure\Persistence\Database\GetData;

use Plinct\Api\ApiFactory;

class GetDataInfrastructureAbstract
{
	/**
	 * @var string
	 */
	const string __LIMIT__ = '200';
	/**
	 * @var ?string
	 */
	protected ?string $query = null;
	/**
	 * @var ?array
	 */
	protected ?array $fields = null;
	/**
	 * @var string
	 */
	protected string $table;
	/**
	 * @var array|null
	 */
	protected ?array $joins = [];
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
	private bool $isCount = false;
	/**
	 * @var bool
	 */
	protected bool $withThings = true;

	private static array $__tables = [];

	/**
	 * @param string $table
	 */
	public function setTable(string $table): void
	{
		$this->table = $table;
		$this->setProperties($table);
	}

	/**
	 * @param bool $withThings
	 */
	public function setWithThings(bool $withThings): void
	{
		$this->withThings = $withThings;
	}

	/**
	 * @return void
	 */
	protected function setQuery(): void
	{
		// FIELDS
		$fields = $this->buildFields();
		// QUERY
		$this->query = "SELECT $fields FROM `$this->table`";
		// LEFT JOIN THING
		if ($this->withThings && isset($this->properties[$this->table]) && !$this->isCount) {
			foreach ($this->properties[$this->table] as $property) {
				if ($property === 'thing') {
					$this->setJoin('thing',"`thing`.idthing = `$this->table`.thing");
				}
			}
		}
		// JOIN
		if ($this->joins) {
			$this->query .= " " . implode(' ',$this->joins);
		}
		// WHERE
		$this->whereCondition();
		// PARAMS
		if ($this->params) {
			$this->finalConditions();
		}
		$this->query .= ";";
	}

	/**
	 * @param string $table
	 */
	protected function setProperties(string $table): void
	{
		if (isset(self::$__tables[$table])) {
			$columnsTable = self::$__tables[$table];
		} else {
			$columnsTable = ApiFactory::request()->server()->connectBd($table)->showColumnsName();
			self::$__tables[$table] = $columnsTable;
		}

		foreach ($columnsTable as $value) {
			$this->properties[$table][] = $value['column_name'] ?? $value['COLUMN_NAME'] ?? null;
		}
	}


	public function setJoin(string $table, string $condition, string $alias = null, string $tableFactor = null): static
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
	 * @param string $joins
	 * @param string|null $table
	 * @return GetDataInfrastructure
	 */
	public function setJoins(string $joins, string $table = null): static
	{
		if ($table === 'thing') {
			array_unshift($this->joins, $joins);
		} else {
			$this->joins[] = $joins;
		}
		return $this;
	}

	/**
	 * @param ?string $property
	 * @return false|string
	 */
	private function isProperty(?string $property): false|string
	{
		if ($property) {
			foreach ($this->properties as $table => $prop) {
				if (in_array($property, $prop)) {
					return $table;
				}
			}
		}
		return false;
	}

	/**
	 */
	protected function buildFields(): string
	{
		if (array_key_exists('fields', $this->params)) {
			$this->fields = explode(',', $this->params['fields']);
		} elseif (!$this->fields) {
			$this->fields = ['*'];
		}
		return implode(',', $this->fields);
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
				if (!!$likeProperty) {
					foreach ($this->properties as $table => $property) {
						if (in_array($likeProperty,$property)) {
							$valuesLike = preg_split('/([,|])/',$value);
							$likeWhere = [];
							foreach ($valuesLike as $item) {
								$likeWhere[] = "(LOWER(TRIM(`$table`.$likeProperty)) LIKE LOWER(TRIM('%$item%')))";
							}
							if (str_contains($value,"|")) {
								$this->where[] = implode(' OR ', $likeWhere);
							} else {
								$this->where[] = implode(' AND ', $likeWhere);
							}
						}
					}
				}
			}
		}
		// PROPERTIES WITH PARAMS
		foreach ($this->params as $propertyNeedle => $propertyValue) {
			foreach ($this->properties as $table => $value) {
				if($value && in_array($propertyNeedle, $value) && (!($propertyNeedle === 'thing') || $table === $this->table)) {
					$propertyValue = is_string($propertyValue) ? addslashes($propertyValue) : $propertyValue;
					if ($propertyValue && str_contains($propertyValue,'|')) {
						$orArray = [];
						foreach (explode('|', $propertyValue) as $orValue) {
							$orArray[] = "`$table`.`$propertyNeedle`='$orValue'";
						}
						$this->where[] = "(" . implode(" OR ", $orArray) . ")";
					} else {
						$this->where[] = "`$table`.$propertyNeedle='$propertyValue'";
					}
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
		if ($groupBy && strtolower(substr($groupBy,0,9)) == 'substring') {
			$this->query .= " GROUP BY $groupBy";
		} else {
			$tableGb = $this->isProperty($groupBy);
			if ($groupBy && $tableGb) {
				$this->query .= " GROUP BY `$tableGb`.$groupBy";
			}
		}
		// ORDER BY
		if ($orderBy) {
			$orderByArray = [];
			foreach (explode(',', $orderBy) as $value) {
				$item = str_replace([' desc', ' asc'], '', trim($value));
				$tableOb = $this->isProperty($item);
				if ($tableOb || $item == 'rand()') {
					$orderByArray[] = ($tableOb ? "`$tableOb`." : null) . trim($value);
				}
			}
			$orderFiltered = implode(',', $orderByArray);
			if (!!$orderFiltered) {
				$this->query .= " ORDER BY $orderFiltered $ordering";
			}
		}
		// LIMIT
		if ($limit != 'none' && $limit != '' && !$this->isCount) {
			$this->query .= " LIMIT $limit";
			// OFFSET
			if ($offset) {
				$this->query .= " OFFSET $offset";
			}
		}
	}
}
