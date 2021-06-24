<?php
namespace Plinct\Api\Server\Relationship;

use Plinct\PDO\Crud;
use Plinct\PDO\PDOConnect;

class RelationshipAbstract extends Crud {
    protected $tableHasPart;
    protected $idHasPart;
    protected $tableIsPartOf;
    protected $idIsPartOf;
    protected $table_has_table;
    protected $params;
    protected $properties;
    protected $hasTypes;

    protected static function table_exists($table): bool {
        return !empty(PDOConnect::run("SHOW tables like '$table';"));
    }

    /**
     * check which properties exists in table
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
            if (array_key_exists($field,$tableHasPartHasTypes) && $this->tableIsPartOf == lcfirst($tableHasPartHasTypes[$field])) {
                return $field;
            }
        }
        return false;
    }

    protected function getColumnName($table, $number) {
        $data = PDOConnect::run("SHOW COLUMNS FROM $table");
        $key = $number -1;
        return $data[$key]['Field'];
    }
}