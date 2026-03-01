<?php
namespace Plinct\Api\Infrastructure\Persistence\Database;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\Server\GetData\GetData;

class RelationshipInfrastructure
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
	 * @param string|null $idHasPart
	 */
	public function setIdHasPart(?string $idHasPart): void
	{
		$this->idHasPart = $idHasPart;
	}

	/**
	 * @param string|null $idIsPartOf
	 */
	public function setIdIsPartOf(?string $idIsPartOf): void
	{
		$this->idIsPartOf = $idIsPartOf;
	}

	/**
	 * @param string|null $typeHasPart
	 */
	public function setTypeHasPart(?string $typeHasPart): void
	{
		$this->typeHasPart = $typeHasPart;
	}

	/**
	 * @param string|null $typeIsPartOf
	 */
	public function setTypeIsPartOf(?string $typeIsPartOf): void
	{
		$this->typeIsPartOf = $typeIsPartOf;
	}

	/**
	 * @param array|null $params
	 */
	public function setParams(?array $params): void
	{
		$this->params = $params;
	}

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

	public function getHasPart(string $idHasPart, string $typeHasPart, string $typeIsPartOf = null, array $params = null): array
	{
		$this->setIdIsPartOf(null);
		$this->setIdHasPart($idHasPart);
		$this->setTypeHasPart(ucfirst($typeHasPart));
		if ($typeIsPartOf) {
			$this->setTypeIsPartOf(ucfirst($typeIsPartOf));
		}
		if ($params) {
			$this->setParams($params);
		}
		return $this->getHasParts();
	}

	public function getHasParts(): array
	{
		$getData = new GetData('thing_has_thing');
		$getData->setParams($this->params + ['limit'=>'none']);
		$getData->setJoin('thing', '`thing`.idthing = `thing_has_thing`.idIsPartOf');
		if ($this->typeIsPartOf) {
			$getData->setJoin(lcfirst($this->typeIsPartOf), "`" . lcfirst($this->typeIsPartOf) . "`.thing = `thing_has_thing`.idIsPartOf");
		}
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
		return ApiFactory::response()->type($this->typeIsPartOf ?? 'Thing')->setData($data)->ready();
	}

	public function getIsPartOf(string $idIsPartOf, string $typeIsPartOf = null, string $typeHasPart = null, array $params = null): array
	{
		$this->setIdHasPart(null);
		$this->setIdIsPartOf($idIsPartOf);
		if ($typeIsPartOf) {
			$this->setTypeIsPartOf(ucfirst($typeIsPartOf));
		}
		if ($typeHasPart) {
			$this->setTypeHasPart(ucfirst($typeHasPart));
		}
		if ($params) {
			$this->setParams($params);
		}
		return $this->getIsPartOfs();
	}

	public function getIsPartOfs(): array
	{
		$getData = new GetData('thing_has_thing');
		$getData->setParams($this->params + ['limit'=>'none']);
		$getData->setJoin('thing', '`thing`.idthing = `thing_has_thing`.idHasPart');
		if ($this->typeHasPart) {
			$getData->setJoin(lcfirst($this->typeHasPart), "`". lcfirst($this->typeHasPart)."`.thing = `thing_has_thing`.idHasPart");
		}
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
		if (isset($this->params['properties']) && str_contains($this->params['properties'], 'isPartOf')) {
			foreach ($data as $key => $item) {
				$this->setIdIsPartOf($item['idthing']);
				$this->setTypeHasPart(null);
				$this->setTypeIsPartOf($item['type']);
				$data[$key]['isPartOf'] = self::getIsPartOfs();
			}
		}
		return ApiFactory::response()->type($this->typeHasPart ?? 'Thing')->setData($data)->ready();
	}

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
	private function propertiesValuesAndIdentifiers(array $data): array
	{
		if (empty($data) || isset($data['error'])) return $data;

		$propertiesValueData = [];
		if (isset($this->params['properties']) && str_contains($this->params['properties'], 'propertyValue')) {
			$idIsPartOfArray = array_column($data, 'idIsPartOf');
			$inTerm = implode(',', $idIsPartOfArray);
			$sql = "SELECT idHasPart, type, t.name, value FROM thing_has_thing tht
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
}
