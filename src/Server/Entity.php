<?php
namespace Plinct\Api\Server;

use ReflectionClass;
use ReflectionException;
use Plinct\PDO\PDOConnect;

abstract class Entity extends Relationship {
    protected $table;
    protected $properties = [];
    protected $hasTypes = [];

    use SchemaTrait;
    
    /**
     * GET
     * @param array $params
     * @return array
     */
    public function get(array $params): array {
        $this->setParams($params);
        if (isset($params['tableHasPart']) && isset($params['idHasPart'])) {
            $data = parent::getRelationship($params['tableHasPart'], $params['idHasPart'], $this->table, $params);
        } else {
            $data = $this->getData($params);
        }
        return $this->buildSchema($params, $data);
    }
    
    protected function getData($params): array {
        $filterGet = new FilterGet($params, $this->table, $this->properties);
        return PDOConnect::run($filterGet->getSqlStatement());
    }
    
    public function post(array $params): array {
        // if relationship
        if (isset($params['tableHasPart']) && isset($params['idHasPart']) ) {
            return parent::postRelationship($params);
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
            return parent::putRelationship($params);
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
            return parent::deleteRelationship($params);
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

    /**
     * @return array
     */
    public function getHasTypes(): array {
        return $this->hasTypes;
    }
}
