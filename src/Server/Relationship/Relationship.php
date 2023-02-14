<?php

declare(strict_types=1);

namespace Plinct\Api\Server\Relationship;

use Plinct\Api\ApiFactory;
use Plinct\PDO\PDOConnect;

class Relationship extends RelationshipAbstract
{
	/**
	 * @param string $tableHasPart
	 * @param string $idHasPart
	 * @param string $tableIsPartOf
	 * @param string|null $idIsPartOf
	 */
  public function __construct(string $tableHasPart, string $idHasPart, string $tableIsPartOf, string $idIsPartOf = null)
  {
    $this->tableHasPart = lcfirst($tableHasPart);
    $this->idHasPart = $idHasPart;
    $this->tableIsPartOf = lcfirst($tableIsPartOf);
		$this->idIsPartOf = $idIsPartOf;
    $this->table_has_table = lcfirst($tableHasPart).'_has_'.lcfirst($tableIsPartOf);
		$this->table = $this->tableIsPartOf;
  }

	/**
	 * @param array $params
	 */
	public function setParams(array $params): void
	{
		$this->params = $params;
	}

	public function post(array $params = [])
	{
		if (self::table_exists($this->table_has_table) && $this->idHasPart) {
			$idHasPartName = parent::getColumnName($this->table_has_table,1);
			$idIsPartOfName = parent::getColumnName($this->table_has_table,2);
			$this->table = $this->table_has_table;
			$paramCreate = array_merge($params, [ $idHasPartName => $this->idHasPart, $idIsPartOfName => $this->idIsPartOf ]);
			return parent::created($paramCreate);
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function delete(): array
	{
		$this->table = $this->table_has_table;
		if ($this->tableHasPart == $this->tableIsPartOf) {
			$idHasPartName = 'idHasPart';
			$idIsPartOfName = 'idIsPartOf';
		} else {
			$idHasPartName = 'id' . $this->tableHasPart;
			$idIsPartOfName = 'id' . $this->tableIsPartOf;
		}
		$where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";
		return parent::erase($where, 1);
	}

  /**
   * @param null $params
   * @return array
   */
  public function getRelationship($params = null): array
  {
    if (parent::table_exists($this->table_has_table)) {
      $orderBy = $params['orderBy'] ?? null;
      $idIsPartOfName = 'id'.$this->tableIsPartOf;
      $idHasPartRelName = parent::getColumnName($this->table_has_table,1);
      $idIsPartOfRelName = parent::getColumnName($this->table_has_table,2);

      $query = "SELECT * FROM $this->tableIsPartOf, $this->table_has_table WHERE $this->table_has_table.$idHasPartRelName=$this->idHasPart AND $this->tableIsPartOf.$idIsPartOfName=$this->table_has_table.$idIsPartOfRelName";

      // IMAGE OBJECT
      $query .= $this->tableIsPartOf == "imageObject" ? " ORDER BY position ASC" : ($orderBy ? " ORDER BY $orderBy" : null);
      // CONTACT POINT
      $query .= $this->tableIsPartOf == "contactPoint" ? " ORDER BY position ASC" : ($orderBy ? " ORDER BY $orderBy" : null);
      // HISTORY
      $query .= $this->tableIsPartOf == "history" ? " ORDER BY datetime DESC" : ($orderBy ? " ORDER BY $orderBy" : null);
      $query .= ";";

      return PDOConnect::run($query);
    }

    return [];
  }

  /**
   * @param array $params
   * @return array
   */
  public function postRelationship(array $params): array
  {
    $this->idIsPartOf = $params['id'] ?? $params['idIsPartOf'] ?? null;

    // CREATE NEW REGISTER ON TABLE IS PART OF
    if (!$this->idIsPartOf) {
      $this->table = $this->tableIsPartOf;
      $data = parent::created($params);

      if (isset($data['error'])) {
        return $data;
      }
      $this->idIsPartOf = PDOConnect::lastInsertId();
    }

    $propertyIsPartOf = $this->propertyIsPartOf();
    // many-to-many relationship type with table_has_table
    if (self::table_exists($this->table_has_table) && $this->idHasPart) {
      $idHasPartName = parent::getColumnName($this->table_has_table,1);
      $idIsPartOfName = parent::getColumnName($this->table_has_table,2);
      $this->table = $this->table_has_table;
      $paramCreate = [ $idHasPartName => $this->idHasPart, $idIsPartOfName => $this->idIsPartOf ];
     return parent::created($paramCreate);
    }
    // one-to-one relationship type
    elseif ($propertyIsPartOf) {
      // update has part
      $this->table = $this->tableHasPart;
      parent::update([$propertyIsPartOf => $this->idIsPartOf], "`id$this->tableHasPart`=$this->idHasPart");

    }

    return $params;
  }

  /**
   * @param $params
   * @return array
   */
  public function putRelationship($params): array
  {
    $this->idIsPartOf = $params['idIsPartOf'] ?? null;

    unset($params['tableIsPartOf']);
    unset($params['idIsPartOf']);
    unset($params['id']);

    if ($this->idIsPartOf && parent::table_exists($this->table_has_table)) {
      $this->table = $this->table_has_table;
      $idHasPartName = 'id' . $this->tableHasPart;
      $idIsPartOfName = 'id' . $this->tableIsPartOf;
      $where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";
      return parent::update($params, $where);
    }

    return ApiFactory::response()->message()->fail()->inputDataIsMissing(["missing idIsPartOf or table_has_table not exists"]);
  }

  /**
   * @param $params
   * @return array|null
   */
  public function deleteRelationship($params = null): ?array
  {
    $this->idIsPartOf = $params['idIsPartOf'] ?? $this->idIsPartOf;

    if ($this->idIsPartOf) {
      $this->table = $this->table_has_table;

      if ($this->tableHasPart == $this->tableIsPartOf) {
	      $idHasPartName = 'idHasPart';
	      $idIsPartOfName = 'idIsPartOf';
      } else {
        $idHasPartName = 'id' . $this->tableHasPart;
        $idIsPartOfName = 'id' . $this->tableIsPartOf;
      }

        $where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";
        return parent::erase($where, 1);
      }

      return null;
  }
}
