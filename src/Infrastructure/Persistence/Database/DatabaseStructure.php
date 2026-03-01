<?php
namespace Plinct\Api\Infrastructure\Persistence\Database;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

readonly class DatabaseStructure
{
	public function __construct(private PDOConnect $pdo)
	{
	}

	public function getBdName(): string
	{
		return $this->pdo::getDbname();
	}

	public function showTables(): array
	{
		$data = $this->pdo::run("SHOW TABLES;");
		if (isset($data['error'])) return $data;
		return array_map(fn($item) => $item['Tables_in_'.$this->getBdName()], $data);
	}
}
