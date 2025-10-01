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
	 * @var array|null
	 */
	private ?array $params = null;

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
	 * @param array|null $params
	 */
	public function setParams(?array $params): void
	{
		$this->params = $params;
	}

	/**
	 * @param string|null $orderBy
	 * @param string $ordering
	 * @return array
	 */
	public function get(string $orderBy = null, string $ordering = 'asc'): array
	{
		$sql = "SELECT * FROM thing_has_thing";
		$where = [];
		$bindValues = [];
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
		if ($this->params) {
			foreach ($this->params as $col => $val) {
				$bindValues[":$col"] = $val;
				$where[] = "`$col`=:$col";
			}
		}
		if (!empty($where)) {
			$sql .= " WHERE " . implode(" AND ", $where);
		}
		if ($orderBy) {
			$sql .= " ORDER BY $orderBy $ordering";
		}
		return PDOConnect::run($sql . ";", $bindValues);
	}

	public function getParts(string $property = 'isPartOf', string $orderBy = null, string $ordering = 'asc'): array
	{
		$data = self::get($orderBy, $ordering);
		$dataItems = [];
		foreach ($data as $value) {
			if ($property == 'hasPart') {
				$type = lcfirst($value['typeIsPartOf']);
				$idthing = $value['idIsPartOf'];
			} else {
				$type = lcfirst($value['typeHasPart']);
				$idthing = $value['idHasPart'];
			}
			$dataItem = ApiFactory::request()->type($type)->get(['thing' => $idthing])->ready();
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
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$caption = $params['caption'] ?? null;
		$position = $params['position'] ?? null;
		$representativeOfPage = $params['representativeOfPage'] ?? null;
		$data = PDOConnect::run("CALL sp_thing_has_thing_insert($this->idHasPart, '$this->typeHasPart', $this->idIsPartOf, '$this->typeIsPartOf', '$caption', '$position', '$representativeOfPage')");
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
		$caption = $params['caption'] ?? null;
		$position = $params['position'] ?? null;
		$representativeOfPage = $params['representativeOfPage'] ?? null;
		$data = PDOConnect::run("CALL sp_thing_has_thing_update($this->idHasPart, '$this->typeHasPart', $this->idIsPartOf, '$this->typeIsPartOf', '$caption', '$position', '$representativeOfPage')");
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
		$data = PDOConnect::run("CALL sp_thing_has_thing_delete($this->idHasPart, '$this->typeHasPart', $this->idIsPartOf, '$this->typeIsPartOf')");
		if (empty($data)) {
			return ApiFactory::response()->message()->success("The relationship of $this->typeHasPart with $this->typeIsPartOf was deleted");
		} else {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}
	}
}
