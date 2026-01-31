<?php
namespace Plinct\Api\Request\Server;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\GetData\GetData;

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
	private ?array $params = [];
	/**
	 * @var array|null
	 */
	private ?array $whereConditions = null;

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
	 * @param array|null $whereConditions
	 * @return void
	 */
	public function setWhereConditions(?array $whereConditions): void
	{
		$this->whereConditions = $whereConditions;
	}

	/**
	 * @return array
	 */
	public function get(): array
	{
		$orderBy = $this->params['orderBy'] ?? null;
		$ordering = $this->params['ordering'] ?? 'asc';
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
		if ($this->whereConditions) {
			foreach ($this->whereConditions as $col => $val) {
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

	/**
	 *
	 */
	public function getSubjectOf(array $thingIds): array
	{
		$namedPlaceholders = [];
		$boundValues = [];
		foreach ($thingIds as $index => $id) {
			$placeholder = ":id_$index";
			$namedPlaceholders[] = $placeholder;
			$boundValues[$placeholder] = $id;
		}
		$placeholdersString = implode(',', $namedPlaceholders);
		$sql = "SELECT * FROM thing_has_thing LEFT JOIN thing ON thing.idthing=thing_has_thing.idHasPart WHERE idIsPartOf IN ($placeholdersString)";
		return $this->normalizeRelationshipItems(PDOConnect::run($sql, $boundValues));
	}

	/**
	 * @param array $typeIsPartOfData
	 * @param string $idHasPart
	 * @param array|null $params
	 * @return array
	 */
	public function getAboutData(array $typeIsPartOfData, string $idHasPart, array $params = null): array
	{
		$namedPlaceholders = [];
		$boundValues = ['idHasPart' => $idHasPart];
		foreach ($typeIsPartOfData as $index => $id) {
			$placeholder = ":id_$index";
			$namedPlaceholders[] = $placeholder;
			$boundValues[$placeholder] = $id;
		}
		$placeholdersString = implode(',', $namedPlaceholders);
		$sql = "SELECT * FROM thing_has_thing LEFT JOIN thing ON thing.idthing=thing_has_thing.idIsPartOf WHERE typeIsPartOf IN ($placeholdersString) AND idHasPart=:idHasPart";
		if (isset($params['orderBy'])) {
			$sql .= " ORDER BY {$params['orderBy']}";
			$sql .= isset($params['ordering']) ? " {$params['ordering']}" : " ASC";
		}
		$sql .= ";";
		return $this->normalizeRelationshipItems(PDOConnect::run($sql, $boundValues));
	}

	/**
	 * Normaliza os metadados de relacionamento (caption, position, etc) para a estrutura PropertyValue
	 * e remove campos de controle interno.
	 *
	 * @param array $dataItems
	 * @return array
	 */
	private function normalizeRelationshipItems(array $dataItems): array
	{
		foreach ($dataItems as $key => $item) {
			// Caption
			if (isset($item['caption'])) {
				$dataItems[$key]['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'caption', 'value' => $item['caption']];
				unset($dataItems[$key]['caption']);
			}
			// Position
			if (isset($item['position'])) {
				$dataItems[$key]['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'position', 'value' => $item['position']];
				unset($dataItems[$key]['position']);
			}
			// RepresentativeOfPage
			if (isset($item['representativeOfPage'])) {
				$dataItems[$key]['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'representativeOfPage', 'value' => $item['representativeOfPage']];
				unset($dataItems[$key]['representativeOfPage']);
			}
			// Limpeza de chaves internas
			unset(
				$dataItems[$key]['idIsPartOf'],
				$dataItems[$key]['typeHasPart'],
				$dataItems[$key]['typeIsPartOf'],
				$dataItems[$key]['idHasPart'],
				$dataItems[$key]['repUniqueFlag']
			);
		}

		return $dataItems;
	}

	/**
	 * @param string $property
	 * @return array
	 */
	public function getHasParts(string $property = 'isPartOf'): array
	{
		$getData = new GetData('thing_has_thing');
		$getData->setParams($this->params + ['limit'=>'none']);
		$getData->setJoin('thing', '`thing`.idthing = `thing_has_thing`.idIsPartOf');
		$getData->setJoin(lcfirst($this->typeIsPartOf), "`". lcfirst($this->typeIsPartOf)."`.thing = `thing_has_thing`.idIsPartOf");
		if ($this->idHasPart) {
			$getData->setParams(['idHasPart' => $this->idHasPart]);
		}
		if ($this->typeHasPart) {
			$getData->setParams(['typeHasPart' => $this->typeHasPart]);
		}
		if ($this->idIsPartOf) {
			$getData->setParams(['idIsPartOf' => $this->idIsPartOf]);
		}
		if ($this->typeIsPartOf) {
			$getData->setParams(['typeIsPartOf' => $this->typeIsPartOf]);
		}
		if (in_array($this->typeIsPartOf,['Article','WebPageElement','WebPage'])) {
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
		}
		$data = $this->propertiesValuesAndIdentifiers($getData->render());

		return ApiFactory::response()->type($this->typeIsPartOf)->setData($data)->ready();
	}

	/**
	 * @return array
	 */
	public function getIsPartOf(): array
	{
		$getData = new GetData('thing_has_thing');
		$getData->setParams($this->params + ['limit'=>'none']);
		$getData->setJoin('thing', '`thing`.idthing = `thing_has_thing`.idHasPart');
		$getData->setJoin(lcfirst($this->typeHasPart), "`". lcfirst($this->typeHasPart)."`.thing = `thing_has_thing`.idHasPart");
		if ($this->idHasPart) {
			$getData->setParams(['idHasPart' => $this->idHasPart]);
		}
		if ($this->typeHasPart) {
			$getData->setParams(['typeHasPart' => $this->typeHasPart]);
		}
		if ($this->idIsPartOf) {
			$getData->setParams(['idIsPartOf' => $this->idIsPartOf]);
		}
		if ($this->typeIsPartOf) {
			$getData->setParams(['typeIsPartOf' => $this->typeIsPartOf]);
		}
		if (in_array($this->typeIsPartOf,['Article','WebPageElement','WebPage'])) {
			$getData->setJoin('creativeWork', 'creativeWork.thing = thing_has_thing.idIsPartOf');
		}
		$data = $this->propertiesValuesAndIdentifiers($getData->render());

		return ApiFactory::response()->type($this->typeHasPart)->setData($data)->ready();
	}

	/**
	 * @param array $data
	 * @return array
	 */
	private function propertiesValuesAndIdentifiers(array $data): array
	{
		if (empty($data)) return $data;

		$propertiesValueData = null;
		if (isset($this->params['properties']) && str_contains($this->params['properties'], 'propertyValue')) {
			$idIsPartOfArray = array_column($data, 'idIsPartOf');
			$inTerm = implode(',', $idIsPartOfArray);
			$sql = "SELECT idHasPart, type, name, value FROM thing_has_thing tht
JOIN thing t  ON t.idthing = tht.idIsPartOf
JOIN propertyValue pv ON pv.thing = t.idthing
WHERE tht.idHasPart IN (" . $inTerm . ")
AND tht.typeIsPartOf = 'PropertyValue';
";
			$propertiesValueData = PDOConnect::run($sql);
		}
		$indexado = [];
		foreach ($propertiesValueData as $item) {
			$indexado[$item['idHasPart']] = $item;
		}
		//
		foreach ($data as $key => $item) {
			$idIsPartOf = $item['idIsPartOf'];
			if (isset($indexado[$idIsPartOf])) {
				$item['identifier'][] = $indexado[$idIsPartOf];
			}
			$data[$key] = $this->buildIdentifiers($item);
		}
		return $data;
	}

	/**
	 * @param array $item
	 * @return array
	 */
	private function buildIdentifiers(array $item): array
	{
		if (isset($item['caption']))	$item['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'caption', 'value' => $item['caption']];
		if (isset($item['position'])) $item['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'position', 'value' => $item['position']];
		if (isset($item['representativeOfPage']))	$item['identifier'][] = ['@type' => 'PropertyValue', 'name' => 'representativeOfPage', 'value' => (bool)$item['representativeOfPage']];
		unset($item['idHasPart']);
		unset($item['typeHasPart']);
		unset($item['idIsPartOf']);
		unset($item['typeIsPartOf']);
		unset($item['repUniqueFlag']);
		unset($item['thing']);
		unset($item['caption']);
		unset($item['position']);
		unset($item['representativeOfPage']);
		return $item;
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
		$data = PDOConnect::run("CALL sp_thing_has_thing_update($this->idHasPart, $this->idIsPartOf, '$caption', '$position', '$representativeOfPage')");
		if (empty($data)) {
			return ApiFactory::response()->message()->success("The relationship was updated");
		} else {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}
	}

	/**
	 * @param string $idHasPart
	 * @param string $idIsPartOf
	 * @return array
	 */
	public function delete(string $idHasPart, string $idIsPartOf): array
	{
		$data = PDOConnect::run("CALL sp_thing_has_thing_delete($idHasPart, $idIsPartOf)");
		if (empty($data)) {
			return ApiFactory::response()->message()->success("The relationship of $this->typeHasPart with $this->typeIsPartOf was deleted");
		} else {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		}
	}
}
