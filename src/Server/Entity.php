<?php
namespace Plinct\Api\Server;

use Plinct\Api\Server\Relationship\Relationship;
use Plinct\Api\Server\Schema\Schema;
use Plinct\PDO\Crud;
use ReflectionClass;
use ReflectionException;
use Plinct\PDO\PDOConnect;

abstract class Entity extends Crud {
    protected $table;
    protected $type;
    protected $properties = [];
    protected $hasTypes = [];

    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array {
        if (isset($params['tableHasPart']) && isset($params['idHasPart'])) {
            $data = (new Relationship($params['tableHasPart'], $params['idHasPart'], $this->table))->getRelationship($params);
        } else {
            $data = $this->getData($params);
        }
        return (new Schema($this->type, $this->properties, $this->hasTypes))->buildSchema($params, $data);
    }
    
    protected function getData($params): array {
        $filterGet = new FilterGet($params, $this->table, $this->properties);
        return PDOConnect::run($filterGet->getSqlStatement());
    }
    
    public function post(array $params): array {
        // if relationship
        if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
            $relationship = new Relationship($params['tableHasPart'], $params['idHasPart'], $this->table);
            unset($params['tableHasPart'],$params['idHasPart']);
            return $relationship->postRelationship($params);
        }
        $message = parent::created($params);
        $lastId = PDOConnect::lastInsertId();
        if ($lastId == '0') {
            return $message;
        } else {
            return [ "id" => $lastId ];
        }
    }
    /**
     * PUT
     * @param array $params
     * @return array
     */
    public function put(array $params): array {
        // if relationship
        if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
            $relationship = new Relationship($params['tableHasPart'], $params['idHasPart'], $this->table);
            unset($params['tableHasPart'],$params['idHasPart']);
            return $relationship->putRelationship($params);
        } 
        unset($params['tableHasPart']);
        $idName = "id".$this->table;
        $idValue = $params['id'];
        unset($params['id']);
        return parent::update($params, "`$idName`=$idValue");
    }
    /**
     * DELETE
     * @param array $params
     * @return array
     */
    public function delete(array $params): array {
        if (isset($params['tableHasPart']) && isset($params['idHasPart']) && isset($params['tableIsPartOf']) && isset($params['idIsPartOf'])) {
            $relationship = new Relationship($params['tableHasPart'], $params['idHasPart'], $this->table);
            unset($params['tableHasPart'],$params['idHasPart']);
            return $relationship->deleteRelationship($params);
        } else {
            $params = array_filter($params);
            $filter = new FilterGet($params, $this->table, $this->properties);
            $this->properties = $filter->getProperties();
            return parent::erase($filter->where(), $filter->limit());
        }
    }
    /**
     * CREATE SQL
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array {
        $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
        $reflection = new ReflectionClass($className);
        $sqlFile = dirname($reflection->getFileName()) . "/" . ucfirst($type) . ".sql";
        // RUN SQL FILE
        if (file_exists($sqlFile)) {
            $data = PDOConnect::run(file_get_contents($sqlFile));
            if (array_key_exists("error", $data)) {
                return $data;
            }
            return [ "message" => "Sql table for ".$type. " created successfully!" ];
        } else {
            return [ "message" => "Sql table for ".$type." not created!" ];
        }
    }

    public function getHasType(): array {
        return $this->hasTypes;
    }
}
