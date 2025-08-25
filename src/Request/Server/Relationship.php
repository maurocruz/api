<?php
namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Relationship
{
	/**
	 * @var ?string
	 */
	private ?string $typeHasPart = null;
	/**
	 * @var ?string
	 */
	private ?string $idHasPart = null;
	/**
	 * @var ?string
	 */
	private ?string $typeIsPartOf = null;
	/**
	 * @var ?string
	 */
	private ?string $idIsPartOf = null;

	/**
	 * @param string|null $typeHasPart
	 */
	public function setTypeHasPart(?string $typeHasPart): void
	{
		$this->typeHasPart = $typeHasPart;
	}

	/**
	 * @param string|null $idHasPart
	 */
	public function setIdHasPart(?string $idHasPart): void
	{
		$this->idHasPart = $idHasPart;
	}

	/**
	 * @param string|null $typeIsPartOf
	 */
	public function setTypeIsPartOf(?string $typeIsPartOf): void
	{
		$this->typeIsPartOf = $typeIsPartOf;
	}

	/**
	 * @param string|null $idIsPartOf
	 */
	public function setIdIsPartOf(?string $idIsPartOf): void
	{
		$this->idIsPartOf = $idIsPartOf;
	}

	/**
	 * @return array
	 */
	public function get(): array
	{
		$sql = "SELECT * FROM thing_has_thing";
		$where = [];
		if ($this->typeHasPart) {
			$bindValues[':typeHasPart'] = $this->typeHasPart;
			$where[] = "`typeHasPart`=:typeHasPart";
		}
		if ($this->idHasPart) {
			$bindValues[':idHasPart'] = $this->idHasPart;
			$where[] = "`idHasPart`=:idHasPart";
		}
		if ($this->typeIsPartOf) {
			$bindValues[':typeIsPartOf'] = $this->typeIsPartOf;
			$where[] = "`typeIsPartOf`=:typeIsPartOf";
		}
		if ($this->idIsPartOf) {
			$bindValues[':idIsPartOf'] = $this->idIsPartOf;
			$where[] = "`idIsPartOf`=:idIsPartOf";
		}
		if (!empty($where)) {
			$sql .= " WHERE ".implode(" AND ", $where);
		}
		$data = PDOConnect::run($sql.";", $bindValues);
		$dataItems = [];
		foreach ($data as $value) {
			$dataItem = ApiFactory::request()->type(lcfirst($value['typeIsPartOf']))->get(['thing' => $value['idIsPartOf']])->ready();
			if (isset($dataItem[0])) {
				$valueItem = $dataItem[0];
				// properties in identifier
				$valueItem['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'caption', 'value' => $value['caption']];
				$valueItem['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'position', 'value' => $value['position']];
				$valueItem['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'representativeOfPage', 'value' => $value['representativeOfPage']];

				$dataItems[] = ApiFactory::response()->type(lcfirst($valueItem['type']))->setData($valueItem)->ready();
			}
		}
		return $dataItems;
	}

	/**
	 * @return array
	 */
	public function post(): array
	{
		$sql = "INSERT INTO thing_has_thing (`typeHasPart`, `idHasPart`, `typeIsPartOf`, `idIsPartOf`) VALUES (:typeHasPart, :idHasPart, :typeIsPartOf, :idIsPartOf)";
		$bindValues[':typeHasPart'] = $this->typeHasPart;
		$bindValues[':idHasPart'] = $this->idHasPart;
		$bindValues[':typeIsPartOf'] = $this->typeIsPartOf;
		$bindValues[':idIsPartOf'] = $this->idIsPartOf;
		$data = PDOConnect::run($sql, $bindValues);
		if (empty($data)) {
			return ApiFactory::response()->message()->success("The relationship of $this->typeHasPart with $this->typeIsPartOf was created");
		} else {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function put(array $params): array
	{
		$setParts = [];
		$bindValues = [];
		foreach ($params as $col => $val) {
			$setParts[] = "`$col`=:$col";
			$bindValues[":$col"] = $val;
		}
		$sqlSet = implode(", ", $setParts);
		$sql = "UPDATE thing_has_thing SET $sqlSet WHERE `typeHasPart`=:typeHasPart AND `idHasPart`=:idHasPart AND `typeIsPartOf`=:typeIsPartOf AND `idIsPartOf`=:idIsPartOf";
		$bindValues[':typeHasPart'] = $this->typeHasPart;
		$bindValues[':idHasPart'] = $this->idHasPart;
		$bindValues[':typeIsPartOf'] = $this->typeIsPartOf;
		$bindValues[':idIsPartOf'] = $this->idIsPartOf;
		$data = PDOConnect::run($sql, $bindValues);
		if (empty($data)) {
			return ApiFactory::response()->message()->success("The relationship of $this->typeHasPart with $this->typeIsPartOf was updated");
		} else {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}
	}

	/**
	 * @return array
	 */
	public function delete(): array
	{
		$sql = "DELETE FROM thing_has_thing WHERE `typeHasPart`=:typeHasPart AND `idHasPart`=:idHasPart AND `typeIsPartOf`=:typeIsPartOf AND `idIsPartOf`=:idIsPartOf";
		$bindValues[':typeHasPart'] = $this->typeHasPart;
		$bindValues[':idHasPart'] = $this->idHasPart;
		$bindValues[':typeIsPartOf'] = $this->typeIsPartOf;
		$bindValues[':idIsPartOf'] = $this->idIsPartOf;
		$data = PDOConnect::run($sql, $bindValues);
		if (empty($data)) {
			return ApiFactory::response()->message()->success("The relationship of $this->typeHasPart with $this->typeIsPartOf was deleted");
		} else {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}
	}
}
