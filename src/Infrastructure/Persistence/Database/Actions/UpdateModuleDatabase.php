<?php
namespace Plinct\Api\Infrastructure\Persistence\Database\Actions;

use Plinct\Api\Infrastructure\Persistence\Database\ConnectDB;

class UpdateModuleDatabase
{
	// UPDATE
	public function update(string $table, array $params, array $whereClause): array
	{
		$bindValues = [];
		$set = [];
		$where = [];

		foreach ($params as $key => $value) {
			$set[] = "`$key` = ?";
			$bindValues[] = $value;
		}

		foreach ($whereClause as $key => $value) {
			$where[] = "`$key` = ?";
			$bindValues[] = $value;
		}

		$set = implode(",", $set);
		$where = implode(" AND ", $where);
		$query = "UPDATE `$table` SET $set WHERE $where";

		$responseData = ConnectDB::run($query, $bindValues);

		if (empty($responseData)) {
			return [ 'status' => true, "message" => "Record in $table updated", 'data' => ['params'=>$params, 'where'=>$whereClause ]];
		} else {
			return [ 'status' => false, "message" => "Record in $table was not updated", 'data' => $responseData['error'] ?? '' ];
		}
	}
}
