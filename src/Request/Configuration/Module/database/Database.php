<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration\Module\database;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Database
{
	/**
	 * @param string $tableName
	 * @return array
	 */
	public function showTableStatus(string $tableName): array {
		$query = "SHOW TABLE STATUS FROM ".PDOConnect::getDbname()." WHERE name='$tableName';";
		$data = PDOConnect::run($query);
		return empty($data) ? ['status'=>'fail','message'=>'table not exists' ] : ['status'=>'success', 'message' => 'table exist', 'data'=> $data];
	}

	/**
	 * @param ...$tables
	 * @return array
	 */
	public function createTable(...$tables): array
	{
		$returns = [];
		foreach ($tables as $table) {
			$fileSql = __DIR__."/sql/$table.sql";
			if (file_exists($fileSql)) {
				$data = PDOConnect::run(file_get_contents($fileSql));
				if (empty($data)) {
					$returns[] = ['status' => 'success', 'message' => "Module $table created or already existing", 'data' => $data];
				} else {
					$returns[] = ['status' => 'fail', 'message' => "Module $table not created", 'data' => $data];
				}
			} else {
				$returns[] = ['status'=>'fail', 'message'=>"File $table.sql not exists"];
			}
		}
		return ['status'=>'complete','message'=>'Modules creation is complete', 'data'=>$returns];
	}

	/**
	 * @param string $tableName
	 * @return array
	 */
	public function showColumnsName(string $tableName): array
	{
		$schema = PDOConnect::getDbname();
		return PDOConnect::run("SELECT column_name FROM information_schema.columns WHERE table_schema = '$schema' AND table_name = '$tableName';");
	}
}
