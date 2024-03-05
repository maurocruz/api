<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server\ConnectBd;

use Plinct\Api\ApiFactory;

class ConnectBd
{

	private string $table;

	public function __construct(string $table)
	{
		$this->table = $table;
	}

	public function created(array $params): array
	{
		return PDOConnect::crud()->setTable($this->table)->created($params);
	}

	public function read(array $params): array
	{
		return PDOConnect::crud()->setTable($this->table)->read($params);
	}

	public function update(array $params): array
	{
		$idname = "id$this->table";
		$idvalue = $params["id$this->table"] ?? null;
		if($idvalue) {
			$data = PDOConnect::crud()->setTable($this->table)->update($params, "`$idname`='$idvalue'");
			if (empty($data)) {
				return ApiFactory::response()->message()->success("The $this->table was updated");
			} elseif (isset($data['error'])) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($data['error']);
			} else {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
		}
	}

	public function delete(array $params): array
	{
		if (empty($params)) {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
		} else {
			$data = PDOConnect::crud()->setTable($this->table)->erase($params);

			if (isset($data['status']) && $data['status'] == 'success') {
				return ApiFactory::response()->message()->success("Item deleted",$data);
			}
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}
	}

	public function lastInsertId(): int {
		return PDOConnect::lastInsertId();
	}

	/**
	 * @return array
	 */
	public function showTableStatus(): array {
		$query = "SHOW TABLE STATUS FROM ".PDOConnect::getDbname()." WHERE name='$this->table';";
		$data = PDOConnect::run($query);
		return empty($data) ? ['status'=>'fail','message'=>'table not exists' ] : ['status'=>'success', 'message' => 'table exist', 'data'=> $data];
	}

	/**
	 * @return array
	 */
	public function showColumnsName(): array
	{
		$schema = PDOConnect::getDbname();
		return PDOConnect::run("SELECT column_name FROM information_schema.columns WHERE table_schema = '$schema' AND table_name = '$this->table';");
	}

	/**
	 * @param string $query
	 * @param $args
	 * @return array
	 */
	public function run(string $query, $args = null): array {
		return PDOConnect::run($query, $args);
	}
}
