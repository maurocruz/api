<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration\Module\database;

use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Database
{
	/**
	 * @return array
	 */
	public function initApplication(): array
	{
		return $this->createTable('user','thing','contactPoint','person','place');
	}

	/**
	 * @param ...$tables
	 * @return array
	 */
	public function createTable(...$tables): array
	{
		$returns = [];
		foreach ($tables as $table){
			$fileSql = __DIR__."/sql/$table.sql";
			if (file_exists($fileSql)) {
				$data = PDOConnect::run(file_get_contents($fileSql));
				if (empty($data)) {
					$returns[] = ['status' => 'success', 'message' => "Table $table created or already existing", 'data' => $data];
				} else {
					$returns[] = ['status' => 'fail', 'message' => "Table $table not created", 'data' => $data];
				}
			} else {
				$returns[] = ['status'=>'fail', 'message'=>"File $table.sql not exists"];
			}
		}
		return ['status'=>'complete','message'=>'Table creation is complete', 'data'=>$returns];
	}
}
