<?php

declare(strict_types=1);

namespace Plinct\Api\Server\Relationship;

use Plinct\PDO\PDOConnect;

class Relationship extends RelationshipAbstract
{
    /**
     * @param $tableHasPart
     * @param $idHasPart
     * @param $tableIsPartOf
     */
    public function __construct($tableHasPart, $idHasPart, $tableIsPartOf)
    {
        $this->tableHasPart = lcfirst($tableHasPart);
        $this->idHasPart = $idHasPart;
        $this->tableIsPartOf = lcfirst($tableIsPartOf);
        $this->table_has_table = lcfirst($tableHasPart).'_has_'.lcfirst($tableIsPartOf);
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

        // one to one relationship type
        $propertyIsPartOf = $this->propertyIsPartOf();
        if ($propertyIsPartOf) {
            // update has part
            $this->table = $this->tableHasPart;
            parent::update([$propertyIsPartOf => $this->idIsPartOf], "`id$this->tableHasPart`=$this->idHasPart");

        }

        // many-to-many relationship type with table_has_table
        elseif (self::table_exists($this->table_has_table)) {
            $idHasPartName = parent::getColumnName($this->table_has_table,1);
            $idIsPartOfName = parent::getColumnName($this->table_has_table,2);
            $this->table = $this->table_has_table;
            $paramCreate = [ $idHasPartName => $this->idHasPart, $idIsPartOfName => $this->idIsPartOf ];
           return parent::created($paramCreate);
        }

        return $params;
    }

    /**
     * @param $params
     * @return array|null
     */
    public function putRelationship($params): ?array
    {
        $this->idIsPartOf = $params['idIsPartOf'] ?? null;

        unset($params['tableIsPartOf']);
        unset($params['idIsPartOf']);
        unset($params['id']);

        if ($this->idIsPartOf) {
            $this->table = $this->table_has_table;
            $idHasPartName = 'id' . $this->tableHasPart;
            $idIsPartOfName = 'id' . $this->tableIsPartOf;
            $where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";
            return parent::update($params, $where);
        }

        return null;
    }

    /**
     * @param $params
     * @return array|null
     */
    public function deleteRelationship($params): ?array
    {
        $this->idIsPartOf = $params['idIsPartOf'] ?? null;

        if ($this->idIsPartOf) {
            $this->table = $this->table_has_table;
            $idHasPartName = 'id' . $this->tableHasPart;
            $idIsPartOfName = 'id' . $this->tableIsPartOf;

            $where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";

            return parent::erase($where, 1);
        }

        return null;
    }
}
