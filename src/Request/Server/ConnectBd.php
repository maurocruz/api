<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Server;

use Plinct\Api\Interfaces\CrudInterface;
use Plinct\Api\Response\ResponseApi;
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
				return ResponseApi::message()->success()->success("The $this->table table was updated");
			} elseif (isset($data['error'])) {
				return ResponseApi::message()->error()->anErrorHasOcurred($data['error']);
			} else {
				return ResponseApi::message()->error()->anErrorHasOcurred($data);
			}
		} else {
			return ResponseApi::message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
		}
	}

	public function delete(array $params): array
	{
		if (empty($params)) {
			return ResponseApi::message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
		} else {
			$data = PDOConnect::crud()->setTable($this->table)->erase($params);

			if (isset($data['status']) && $data['status'] == 'success') {
				return ResponseApi::message()->success()->success("Item deleted",$data);
			}
			return ResponseApi::message()->error()->anErrorHasOcurred($data);
		}
	}

	public function lastInsertId(): string {
		return PDOConnect::lastInsertId();
	}

	public function run(string $query, $args = null): array {
		return PDOConnect::run($query, $args);
	}
}