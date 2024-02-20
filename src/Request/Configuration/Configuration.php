<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Configuration
{
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
	 * @param ?string $name
	 * @return string[]
	 */
	public function createModule(?string $name): array
	{
		if (!$name) return ['message'=>'Module was not created! Name is null!'];
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
	}
}