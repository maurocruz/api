<?php
namespace Plinct\Api\Server\Schema;

use Plinct\PDO\PDOConnect;

abstract class SchemaAbstract
{
	/**
	 * @var string
	 */
  protected string $context = "https://schema.org";
	/**
	 * @var array
	 */
  protected array $schema = [];
	/**
	 * @var array
	 */
  protected $properties = [];
	/**
	 * @var array
	 */
  protected $hasTypes = [];
	/**
	 * @var string
	 */
  protected string $tableHasPart;
	/**
	 * @var string|null
	 */
  protected ?string $idHasPart = null;
	/**
	 * @var string|null
	 */
  protected ?string $table = null;
	/**
	 * @var string
	 */
  protected string $type;
	/**
	 * @var array
	 */
  protected array $params;
	/**
	 * @var array
	 */
	protected array $attachedProperty = [];

  /**
   * @param string $propertiesParams
   */
  public function setProperties(string $propertiesParams): void {
    $propertiesArray = explode(',',$propertiesParams);
    $this->properties = array_merge($propertiesArray, $this->properties);
  }

  /**
   *
   */
  public function setHasTypes(): void {
    $enabledHasType = [];
    foreach ($this->properties as $value) {
			$property = strstr($value,":") !== false ? strstr($value,":",true) : $value;

			$attachedProperty = strstr($value,":") !== false ? substr(strstr($value,":"),1) : null;
			if ($attachedProperty) {
				$this->attachedProperty = [$property=>$attachedProperty];
			}

      if (array_key_exists($property,$this->hasTypes)) {
        $enabledHasType[$property] = $this->hasTypes[$property];
      }
    }
    $this->hasTypes = $enabledHasType;
  }

  /**
   * @param $columnName
   * @return bool
   */
  protected function ifExistsColumn($columnName): bool {
    $columns = PDOConnect::run("SHOW COLUMNS FROM `$this->tableHasPart`");
    foreach ($columns as $value) {
      if ($value['Field'] == $columnName) return true;
    }
    return false;
  }
}
