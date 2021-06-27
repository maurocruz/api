<?php
namespace Plinct\Api\Server\Schema;

use Plinct\PDO\PDOConnect;

abstract class SchemaAbstract {
    protected $context = "https://schema.org";
    protected $schema = [];
    protected $properties = [];
    protected $hasTypes = [];

    protected $tableHasPart;
    protected $idHasPart;
    protected $table;
    protected $type;
    protected $params;

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
            if (array_key_exists($value,$this->hasTypes)) {
                $enabledHasType[$value] = $this->hasTypes[$value];
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