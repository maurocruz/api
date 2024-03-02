<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration\Module;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Configuration\Module\database\Database;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Module
{
	const SQL_DIR = __DIR__.'/database/sql/';

	/**
	 * @return Database
	 */
	public function database(): Database
	{
		return new Database();
	}

	/**
	 * @return array
	 */
	public function initApplication(): array
	{
		return $this->database()->createTable('user','thing','person','place','contactPoint','creativeWork','mediaObject','imageObject');
	}


	/**
	 * @param ?string $name
	 * @return string[]
	 */
	public function installModule(?string $name): array
	{
		if (!$name) return ['message'=>'Module was not created! Name is null!'];
		$tableName = lcfirst($name);
		$checkTable = ApiFactory::server()->connectBd($tableName)->showTableStatus();
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
