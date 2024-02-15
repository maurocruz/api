<?php
declare(strict_types=1);
namespace Plinct\Api\Server\Relationship;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

class Relationship extends RelationshipAbstract
{
	/**
	 * @param string $tableHasPart
	 * @param string $idHasPart
	 * @param string $tableIsPartOf
	 * @param int|null $idIsPartOf
	 */
  public function __construct(string $tableHasPart, string $idHasPart, string $tableIsPartOf, int $idIsPartOf = null)
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
	/**
	 * @param array $params
	 * @return false|string[]
	 */
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
   * WHEN TABLE_HAS_PART OR IDCOLUMN_IS_PART_OF EXISTS
   * @param null $params
   * @return array
   */
  public function getRelationship($params = null): array
  {
		$query = null;
	  $orderBy = $params['orderBy'] ?? null;
		// IF TABLE_HAS_PART EXISTS
    if (parent::table_exists($this->table_has_table)) {
      $idIsPartOfName = 'id'.$this->tableIsPartOf;
      $idHasPartRelName = parent::getColumnName($this->table_has_table,1);
      $idIsPartOfRelName = parent::getColumnName($this->table_has_table,2);
      $query = "SELECT * FROM $this->tableIsPartOf, $this->table_has_table WHERE $this->table_has_table.$idHasPartRelName=$this->idHasPart AND $this->tableIsPartOf.$idIsPartOfName=$this->table_has_table.$idIsPartOfRelName";
			if ($orderBy) {
				$query .= " ORDER BY $orderBy";
			} else {
				// IMAGE OBJECT
				$query .= $this->tableIsPartOf == "imageObject" ? " ORDER BY position ASC" : null;
				// HISTORY
				$query .= $this->tableIsPartOf == "history" ? " ORDER BY datetime DESC" : null;
			}
      $query .= ";";
    }
		// IF ISPARTOF OU IDTABLEHASPART COLUMN EXISTS
		else {
			$columnName = null;
	    $columnExists = PDOConnect::run("SHOW COLUMNS FROM `$this->table` LIKE 'isPartOf'");
			if (!empty($columnExists)) {
				$columnName = 'isPartOf';
			} else {
				$columnExists2 = PDOConnect::run("SHOW COLUMNS FROM `$this->table` LIKE 'id$this->tableHasPart'");
				if(!empty($columnExists2)) {
					$columnName = "id".$this->tableHasPart;
				}
			}
			if ($columnName) {
				$query = "SELECT * FROM `$this->table` WHERE $columnName=$this->idHasPart";
				if ($orderBy) {
					$query .= " ORDER BY $orderBy;";
				}
			}
    }
	  return $query ? PDOConnect::run($query) : [];
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
    $this->idIsPartOf = (int) $params['idIsPartOf'] ?? null;
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
