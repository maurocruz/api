<?php
namespace Plinct\Api\Server;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Maintenance {
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
