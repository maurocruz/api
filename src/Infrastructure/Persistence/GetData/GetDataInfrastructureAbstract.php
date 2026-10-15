<?php
namespace Plinct\Api\Infrastructure\Persistence\GetData;

use Plinct\Api\Domain\Entities\Entities;
use ReflectionException;

abstract class GetDataInfrastructureAbstract
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
	 * @var string|null
	 */
	protected ?string $tableAlias = null;
	/**
	 * @var array
	 */
	protected array $tables = [];
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

	/**
	 * @param string $table
	 */
	public function setTable(string $table): void
	{
		$this->table = $table;
		$this->tables[] = $table;
	}

	/**
	 * @param string $tableAlias
	 */
	public function setTableAlias(string $tableAlias): void
	{
		$this->tableAlias = $tableAlias;
	}

	/**
	 * @param bool $withThings
	 * @return GetDataInfrastructureAbstract
	 */
	public function setWithThings(bool $withThings): static
	{
		$this->withThings = $withThings;
		return $this;
	}

	/**
	 * @return void
	 * @throws ReflectionException
	 */
	protected function setQuery(): void
	{
		// FIELDS
		$fields = $this->buildFields();
		// QUERY
		$this->query = "SELECT $fields FROM `$this->table`";
		if ($this->tableAlias) {
			$this->query .= " AS $this->tableAlias";
		}
		// JOIN
		$this->declareJoins();
		// WHERE
		$this->whereCondition();
		// PARAMS
		if ($this->params) {
			// GROUP BY
			$this->declareGroupBy();
			// ORDER BY
			self::declareOrderBy();
		}
		// LIMIT AND OFFSET
		self::declareLimit();

		$this->query .= ";";
	}

	/**
	 * @param string $property
	 * @return false|mixed
	 * @throws ReflectionException
	 */
	private function isProperty(string $property): mixed
	{
		foreach ($this->tables as $table) {
			if (Entities::isOwnProperty(ucfirst($table), $property)) {
				return $table;
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
		$fields = array_map('htmlspecialchars_decode',$this->fields);
		return implode(',', $fields);
	}

	private function declareJoins(): void
	{
		$entity = Entities::getEntity(ucfirst($this->table));
		if ($this->withThings && $entity && property_exists($entity, 'thing') && !$this->isCount !== null) {
		 array_unshift($this->joins, ['table'=> 'thing', 'condition' => "`thing`.idthing = `$this->table`.thing"]);
		}

		foreach ($this->joins as $join) {
			$statement = " ";
			$statement .= isset($join['factor']) ? " {$join['factor']} " : null;
			$statement .= "JOIN `{$join['table']}`";
			$statement .= isset($join['alias']) ? " AS {$join['alias']}" : null;
			$statement .= " ON {$join['condition']}";
			$this->query .= $statement;
			if(!in_array($join['table'], $this->tables)) {
				$this->tables[] = $join['table'];
			}
		}
	}

	/**
	 *
	 * @throws ReflectionException
	 */
	protected function whereCondition(): void
	{
		//  WHERE WITH PARAMS
		if (array_key_exists('where',$this->params)) {
			$this->where[] = $this->params['where'];
		}

		foreach ($this->params as $key => $value) {
			$currentTable = $this->isProperty($key);
			if (!$currentTable) {
				continue;
			}

			// WHERE CONDITION
			if ($value && str_contains($value,'|')) {
				$orArray = [];
				foreach (explode('|', $value) as $orValue) {
					$orArray[] = "`$currentTable`.$key='$orValue'";
				}
				$this->where[] = "(" . implode(" OR ", $orArray) . ")";
			} else {
				$this->where[] = "`$currentTable`.$key='$value'";
			}
		}

		// LIKE STATEMENT
		self::declareLike();

		$this->query .= !empty($this->where) ? " WHERE " . implode(" AND ", array_filter($this->where)) : null;
	}

	// LIKE
	/**
	 * @throws ReflectionException
	 */
	protected function declareLike(): void
	{
		foreach ($this->params as $key => $value) {
			$likeProperty = stristr($key,"like", true);
			$currentTable = $this->isProperty($likeProperty);

			if (!$currentTable) {
				continue;
			}

			if ($likeProperty) {
				$valuesLike = preg_split('/([,|])/', $value);
				$likeWhere = [];
				foreach ($valuesLike as $item) {
					$likeWhere[] = "(LOWER(TRIM(`$currentTable`.$likeProperty)) LIKE LOWER(TRIM('%$item%')))";
				}
				if (str_contains($value, "|")) {
					$this->where[] = implode(' OR ', $likeWhere);
				} else {
					$this->where[] = implode(' AND ', $likeWhere);
				}
			}
		}
	}

	// GROUP BY
	/**
	 * @throws ReflectionException
	 */
	protected function declareGroupBy(): void
	{
		$groupBy = $this->params['groupBy'] ?? null;
		if (!$groupBy) {
			return;
		}
		if (strtolower(substr($groupBy,0,9)) == 'substring') {
			$this->query .= " GROUP BY $groupBy";
		} else {
			if ($this->isProperty($groupBy)) {
				$this->query .= " GROUP BY " . (Entities::isOwnProperty(ucfirst($this->table), $groupBy) ? "`$this->table`.$groupBy" : $groupBy);
			}
		}
	}

	// ORDER BY
	/**
	 * @throws ReflectionException
	 */
	private function declareOrderBy(): void
	{
		$orderBy = $this->params['orderBy'] ?? '';
		$ordering = $this->params['ordering'] ?? 'asc';
		if ($orderBy) {
			$explodeOrderBy = explode(',', $orderBy);
			$explodeOrdering = explode(',', $ordering);
			$orderByArray = [];
			foreach ($explodeOrderBy as $key => $value) {
				$item = str_replace([' desc', ' asc'], '', trim($value));
				if ($this->isProperty($item) || $item == 'rand()') {
					foreach ($this->tables as $table) {
						if (Entities::isOwnProperty($table, $item) && !($table === 'creativeWork' && $item === 'position')) {
							$orderByArray[] = "`$table`.$item". ' ' . ($explodeOrdering[$key] ?? ' '.$ordering);
						}
					}
				}
			}
			$orderFiltered = implode(',', $orderByArray);
			if (!!$orderFiltered) {
				$this->query .= " ORDER BY $orderFiltered";
			}
		}
	}

	// LIMIT AND OFFSET
	private function declareLimit(): void
	{
		$limit = $this->params['limit'] ?? self::__LIMIT__;
		$offset = $this->params['offset'] ?? 0;
		if ($limit != 'none' && $limit != '' && !$this->isCount) {
			$this->query .= " LIMIT $limit";
			if ($offset) {
				$this->query .= " OFFSET $offset";
			}
		}
	}
}
