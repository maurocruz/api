<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\Interfaces\CrudInterface;
use Plinct\PDO\PDOConnect;

class ConnectBd implements CrudInterface
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
		return PDOConnect::crud()->setTable($this->table)->read(
			$params['fields'] ?? '*',
			$params['where'] ?? null,
			$params['groupBy'] ?? null,
			$params['orderBy'] ?? null,
			$params['limit'] ?? null,
			$params['offset'] ?? null,
			$params['args'] ?? null
		);
	}

	public function update(array $params): array
	{
		$idname = "id$this->table";
		$idvalue = $params["id$this->table"] ?? null;
		if($idvalue) {
			$data = PDOConnect::crud()->setTable($this->table)->update($params, "`$idname`='$idvalue'");
			if (empty($data)) {
				return ApiFactory::response()->message()->success()->success("The $this->table table was updated");
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
				return ApiFactory::response()->message()->success()->success("Item deleted",$data);
			}
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}
	}

	public function lastInsertId(): string {
		return PDOConnect::lastInsertId();
	}

	public function run(string $query, $args = null): array {
		return PDOConnect::run($query, $args);
	}
}