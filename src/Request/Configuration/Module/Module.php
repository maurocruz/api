<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration\Module;

use Plinct\Api\Request\Configuration\Module\database\Database;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Module
{
	const SQL_DIR = __DIR__.'/sql/';

	/**
	 * @return Database
	 */
	public function database(): Database
	{
		return new Database();
	}
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
	 * @param ?string $name
	 * @return string[]
	 */
	public function install(?string $name): array
	{
		if (!$name) return ['message'=>'Module was not created! Name is null!'];
		$tableName = lcfirst($name);
		$checkTable = $this->showTableStatus($tableName);
		if ($checkTable['status'] === 'fail') {
			$sqlFile = self::SQL_DIR.lcfirst($name).".sql";
			if (file_exists($sqlFile)) {
				$data = PDOConnect::run(file_get_contents($sqlFile));
				if(empty($data)) {
					return ['status'=>'success','message'=>'Module has been created'];
				} else {
					return ['status'=>'fail','message'=>'fail','data'=>$data];
				}
			} else {
				return ['status'=>'fail','message'=>'Module was not created! SQL file does not exists'];
			}
		} else {
			return $checkTable;
		}
	}
}
