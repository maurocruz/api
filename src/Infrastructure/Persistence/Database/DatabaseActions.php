<?php
namespace Plinct\Api\Infrastructure\Persistence\Database;

use Plinct\Api\Infrastructure\Persistence\GetData\GetDataInfrastructure;
use ReflectionException;

class DatabaseActions extends Crud
{

	public function showTables(): array
	{
		$data = $this::run("SHOW TABLES;");
		if (isset($data['error'])) return $data;
		return array_map(fn($item) => $item['Tables_in_'.$this::getDbname()], $data);
	}

	/**
	 * @param string $property
	 * @param array $params
	 * @return array|null
	 * @throws ReflectionException
	 */
	public function getProperties(string $property, array $params): ?array
	{
		$data = new GetDataInfrastructure();
		$data->setTable($property);
		$data->setParams($params);
		return $data->render();
	}


	public function createRelationship(string $idHasPart, string $typeHasPart, string $idIsPartOf, string $typeIspartOf, string $caption = '', int $position = 0, int $representativeOfPage = 0): array
	{
		$callProcedure = $this::run("CALL sp_thing_has_thing_insert($idHasPart, '$typeHasPart', $idIsPartOf, '$typeIspartOf', '$caption', $position, $representativeOfPage)");

		if (isset($callProcedure['error'])) {
			return ['status' => false, 'message'=> 'Relationship was not created', 'data'=> $callProcedure['error']];
		}
		return ['status' => true, 'message'=> 'Relationship created successfully', 'data'=> $callProcedure];
	}

	/**
	 * @param string $idHasPart
	 * @param string $idIsPartOf
	 * @param string|null $caption
	 * @param int|null $position
	 * @param string $representativeOfPage
	 * @return array
	 */
	public function updateRelationship(string $idHasPart, string $idIsPartOf, string $caption = null, int $position = null, string $representativeOfPage = ''): array
	{
		$callProcedure = $this::run("CALL sp_thing_has_thing_update($idHasPart, $idIsPartOf, '$caption', $position, '$representativeOfPage')");

		if (isset($callProcedure['error'])) {
			return ['status' => false, 'message'=> 'Relationship was not updated', 'data'=> $callProcedure['error']];
		}
		return ['status' => true, 'message'=> 'Relationship updated successfully', 'data'=> $callProcedure];
	}

	/**
	 * @param string $idHasPart
	 * @param string $idIsPartOf
	 * @return array
	 */
	public function removeRelationship(string $idHasPart, string $idIsPartOf): array
	{
		$callProcedure =  $this::run("CALL sp_thing_has_thing_delete($idHasPart, $idIsPartOf)");

		if (isset($callProcedure['error'])) {
			return ['status' => false, 'message'=> 'Relationship was not deleted', 'data'=> $callProcedure['error']];
		}
		return ['status' => true, 'message'=> 'Relationship deleted successfully', 'data'=> $callProcedure];
	}
}
