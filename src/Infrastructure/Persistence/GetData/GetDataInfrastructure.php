<?php
namespace Plinct\Api\Infrastructure\Persistence\GetData;

use Plinct\Api\Infrastructure\Persistence\Database\ConnectDB;
use ReflectionException;

class GetDataInfrastructure extends GetDataInfrastructureAbstract
{

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
	 * @param string|null $alias
	 * @return GetDataInfrastructure
	 */
	public function setLeftJoin(string $table, string $condition, string $alias = null): static
	{
		$this->setJoin($table, $condition, $alias, 'LEFT');
		return $this;
	}

	public function setJoin(string $table, string $condition, string $alias = null, string $tableFactor = null): static
	{
		if ($alias) {
			$condition = str_replace("`$table`", $alias, $condition);
			$this->setJoins("$tableFactor JOIN `$table` as $alias ON $condition", $table);
		} else {
			$this->setJoins("$tableFactor JOIN `$table` ON $condition", $table);
		}
		return $this;
	}

	/**
	 * @param $params
	 * @return GetDataInfrastructure
	 */
	public function setParams($params): static
	{
		$this->params = $this->params + $params;
		return $this;
	}

	/**
	 * @param ?string $where
	 * @return GetDataInfrastructure
	 */
	public function setWhere(?string $where): static
	{
		if ($where) {
			$this->where[] = $where;
		}
		return $this;
	}

	public function getQuery(): string
	{
		return $this->query;
	}

	/**
	 * @return array
	 * @throws ReflectionException
	 */
	public function render(): array
	{
		$this->setQuery();
		$data = ConnectDB::run($this->getQuery());
		if (!isset($data['error'])) {
			return ['status' => true, 'message' => "Get data in '$this->table' item successfuly", 'data' => $data];
		} else {
			return ['status' => false, 'message' => "Get data in '$this->table' failed", 'data' => $data];
		}
	}
}
