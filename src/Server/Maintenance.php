<?php
namespace Plinct\Api\Server;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Maintenance {

	const SQL_DIR = __DIR__.'/sql/';
	/**
	 * @param string $table
	 * @return array
	 */
	public function showTableStatus(string $table): array {
		$query = "SHOW TABLE STATUS FROM ".PDOConnect::getDbname()." WHERE name='$table';";
		$data = PDOConnect::run($query);
		return empty($data) ? [ 'message'=>'table not exists' ] : ['status'=>'success', 'message' => 'table exist', 'data'=> $data];
	}

	public function setBasicConfiguration(): array {
		$data = PDOConnect::run(file_get_contents(self::SQL_DIR.'basic.sql'));
		if(empty($data)) {
			return ['status'=>'success', 'message'=>'Basic SQL schema has been created'];
		} else {
			return ['status'=>'fail', 'message'=>'Basic SQL schema was not created','data'=>$data ];
		}
	}

	/**
	 * @param string $name
	 * @return string[]
	 */
	public function createTable(string $name): array
	{
		$sqlFile = self::SQL_DIR.$name.".sql";
		if (file_exists($sqlFile)) {
			$data = PDOConnect::run(file_get_contents($sqlFile));
			if(empty($data)) {
				return ['message'=>'Table has been created'];
			} else {
				return ['message'=>'fail','data'=>$data];
			}
		}
		return ['message'=>'Table was not created'];
	}
	/**
	 * @param $type
	 * @return string[]
	 */
  public function createSqlTable($type): array {
    $table = substr_replace($type, strtolower(substr($type, 0, 1)), 0, 1);
    $query = "SHOW TABLES LIKE '$table';";
    $data = PDOConnect::run($query);
    if (empty($data)) {
      $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
      return (new $className())->createSqlTable($type);
    } else {
      return [ "message" => $type. " already exists" ];
    }
  }

	/**
	 * @return array
	 */
	public static function setTableHasImageObject(): array
	{
		$table_schema = PDOConnect::getDbname();
		return PDOConnect::run("SELECT table_name as tableName FROM information_schema.tables WHERE table_schema = '$table_schema' AND table_name LIKE '%_has_imageObject';");
	}
}
