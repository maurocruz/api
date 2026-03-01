<?php
namespace Plinct\Api\Infrastructure\Persistence\Database\GetData;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

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
	 */
	public function render(): array
	{
		$this->setQuery();
		return PDOConnect::run($this->getQuery());
	}
}
