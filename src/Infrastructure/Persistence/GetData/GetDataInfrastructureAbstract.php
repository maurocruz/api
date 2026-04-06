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
		// LEFT JOIN THING
		$entity = Entities::getEntity(ucfirst($this->table));
		if ($this->withThings && $entity && property_exists($entity, 'idthing') && !$this->isCount !== null) {
			$this->setJoin('thing',"`thing`.idthing = `$this->table`.thing");
		}
		// JOIN
		if ($this->joins) {
			$this->query .= " " . implode(' ',$this->joins);
		}
		// WHERE
		$this->whereCondition();
		// PARAMS
		if ($this->params) {
			// GROUP BY
			$this->declareGroupBy();
			// ORDER BY
			self::declareOrderBy();
			// LIMIT AND OFFSET
			self::declareLimit();
		}
		$this->query .= ";";
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
		$this->tables[] = $table;
		return $this;
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
		return implode(',', $this->fields);
	}

	/**
	 *
	 * @throws ReflectionException
	 */
	protected function whereCondition(): void
	{
		foreach ($this->params as $key => $value) {
			$currentTable = $this->isProperty($key);
			if (!$currentTable) {
				continue;
			}

			//  WHERE WITH PARAMS
			if ($key == 'where') {
				$this->where[] = $value;
			}

			// WHERE CONDITION
			if (str_contains($value,'|')) {
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
		$ordering = $this->params['ordering'] ?? '';

		if ($orderBy) {
			$explodeOrderBy = explode(',', $orderBy);
			$explodeOrdering = explode(',', $ordering);
			$orderByArray = [];
			foreach ($explodeOrderBy as $key => $value) {
				$item = str_replace([' desc', ' asc'], '', trim($value));
				if ($this->isProperty($item) || $item == 'rand()') {
					$orderByArray[] = (
						Entities::isOwnProperty(ucfirst($this->table), $item)
							? "`$this->table`."
							: null
						) . $item . ' ' . ($explodeOrdering[$key] ?? ' asc');
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
