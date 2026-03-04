<?php
namespace Plinct\Api\Infrastructure\Persistence\Database;

use Plinct\Api\Infrastructure\Persistence\Database\GetData\GetDataInfrastructure;

class DatabaseActions extends ConnectDB
{
	protected string $table;
	protected array $params = [];

	/**
	 * @param string $table
	 */
	public function setTable(string $table): void
	{
		$this->table = $table;
	}

	public function read(string $table, array $params = []): GetDataInfrastructure
	{
		$this->setTable($table);
		$getData = new GetDataInfrastructure();
		$getData->setTable($table);
		$getData->setParams($params);
		return $getData;
	}

	public function create(string $table, array $params = []): array
	{
		$this->setTable($table);
		$this->setParams($params);
		$names = null;
		$values = null;
		$bindValues = null;
		if (empty($this->params)) {
			return [ 'status' => false, "message" => "Record in $this->table not created because data is empty" ];
		}
		// query
		foreach ($this->params as $key => $value) {
			$names[] = "`$key`";
			$values[] = "?";
			$bindValues[] = $value;
		}
		$columns = implode(",", $names);
		$rows = implode(",", $values);
		$query = "INSERT INTO `$table` ($columns) VALUES ($rows)";
		$responseData = parent::run($query, $bindValues);
		if (empty($responseData)) {
			$lastId = parent::lastInsertId();
			$itemData = $this->read($table, ["id$table" => $lastId])->render();
			return [ 'status' => true, "message" => "Record in $table created", "data" => $itemData[0] ?? [] ];
		} else {
			return [ 'status' => false, "message" => "Record in $table was not created" ];
		}
	}

	public function showTables(): array
	{
		$data = $this::run("SHOW TABLES;");
		if (isset($data['error'])) return $data;
		return array_map(fn($item) => $item['Tables_in_'.$this::getDbname()], $data);
	}

	/**
	 * @param array $params
	 */
	protected function setParams(array $params): void
	{
		$columnsTable = self::showColumnsName();
		$newParams = [];
		foreach ($columnsTable as $value) {
			$columnName = $value['column_name'] ?? $value['COLUMN_NAME'] ?? null;
			if (array_key_exists($columnName,$params)) {
				$newParams[$columnName] = $params[$columnName];
			}
		}
		$this->params = $newParams;
	}

	/**
	 * @return array
	 */
	public function showColumnsName(): array
	{
		$schema = parent::getDbname();
		return parent::run("SELECT column_name FROM information_schema.columns WHERE table_schema = '$schema' AND table_name = '$this->table';");
	}
}
