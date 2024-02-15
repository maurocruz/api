<?php
declare(strict_types=1);
namespace Plinct\Api\Server\Relationship;

use Plinct\Api\Request\Server\ConnectBd\Crud;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;

abstract class RelationshipAbstract extends Crud
{
	/**
	 * @var string|null
	 */
	protected ?string $tableHasPart;
	/**
	 * @var string|null
	 */
  protected ?string $idHasPart;
	/**
	 * @var string|null
	 */
  protected ?string $tableIsPartOf;
	/**
	 * @var int|null
	 */
	protected ?int $idIsPartOf;
	/**
	 * @var string|null
	 */
  protected ?string $table_has_table;
	/**
	 * @var array
	 */
  protected array $params;
	/**
	 * @var array
	 */
  protected array $properties;
	/**
	 * @var array
	 */
  protected array $hasTypes;

  /**
   * @param $table
   * @return bool
   */
  protected static function table_exists($table): bool {
    return !empty(PDOConnect::run("SHOW tables like '$table';"));
  }

  /**
   * Check which properties exist in table
   * @return mixed
   */
  protected function propertyIsPartOf() {
    // show columns
    $columns = PDOConnect::run("SHOW COLUMNS FROM `$this->tableHasPart`");
    // check has types in table has
    $tableHasPartObjectName = "Plinct\\Api\\Type\\".ucfirst($this->tableHasPart);
    $tableHasPartHasTypes = (new $tableHasPartObjectName())->getHasType();
    // compare with colunm of table
    foreach ($columns as $valueColumn) {
      $field = $valueColumn['Field'];
      if (array_key_exists($field,$tableHasPartHasTypes) && is_string($tableHasPartHasTypes[$field]) && $this->tableIsPartOf == lcfirst($tableHasPartHasTypes[$field])) {
        return $field;
      }
    }
    return false;
  }

  /**
   * @param $table
   * @param $number
   * @return mixed
   */
  protected function getColumnName($table, $number) {
    $data = PDOConnect::run("SHOW COLUMNS FROM $table");
    $key = $number -1;
    return $data[$key]['Field'];
  }
}