<?php
namespace Plinct\Api\Server\Relationship;

use Plinct\Api\Server\FilterGet;
use Plinct\PDO\PDOConnect;

class Relationship extends RelationshipAbstract {

    public function __construct($tableHasPart, $idHasPart, $tableIsPartOf) {
        $this->tableHasPart = lcfirst($tableHasPart);
        $this->idHasPart = $idHasPart;
        $this->tableIsPartOf = lcfirst($tableIsPartOf);
        $this->table_has_table = lcfirst($tableHasPart).'_has_'.lcfirst($tableIsPartOf);
    }

    public function getRelationship($params = null): array {
        if (parent::table_exists($this->table_has_table)) {
            $filter = new FilterGet($params, $this->tableIsPartOf);
            $orderBy = $filter->orderBy();
            $idIsPartOfName = 'id'.$this->tableIsPartOf;
            $idHasPartRelName = parent::getColumnName($this->table_has_table,1);
            $idIsPartOfRelName = parent::getColumnName($this->table_has_table,2);
            $query = "SELECT * FROM $this->tableIsPartOf, $this->table_has_table WHERE $this->tableIsPartOf.$idIsPartOfName = $this->table_has_table.$idIsPartOfRelName AND $this->table_has_table.$idHasPartRelName = $this->idHasPart";
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

    public function postRelationship(array $params): array {
        $this->idIsPartOf = $params['id'] ?? null;
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
        // with many relationship type
        else {
            // many to many
            if (self::table_exists($this->table_has_table)) {
                $idHasPartName = parent::getColumnName($this->table_has_table,1);
                $idIsPartOfName = parent::getColumnName($this->table_has_table,2);
                $this->table = $this->table_has_table;
                $paramCreate = [ $idHasPartName => $this->idHasPart, $idIsPartOfName => $this->idIsPartOf ];
               return parent::created($paramCreate);
            }
            // many to many
            else {
                $this->table = $this->tableIsPartOf;
                $idHasPartName = parent::getColumnName('hasPart', $this->tableHasPart);
                $params[$idHasPartName] = $params['idHasPart'];
                unset($params['tableHasPart']);
                unset($params['idHasPart']);
                return parent::created($params);
            }
        }
        return $params;
    }
    
    public function putRelationship($params): ?array {
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

    public function deleteRelationship($params): ?array {
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
    
    /*protected function relationshipsInSchema($valueData, $valueProperty) {
        // VERIFY IS CLASS OBJECT IS PART OF TYPE EXISTS
        $typeIsPartOf = $this->hasTypes[$valueProperty] === true ? $valueData[$valueProperty.'Type'] : $this->hasTypes[$valueProperty];
        $this->tableIsPartOf = lcfirst($typeIsPartOf);
        $typeIsPartOfObject = self::getTypeObject($typeIsPartOf);
        // IF EXISTS
        if ($typeIsPartOfObject) {
            // one to one
            if (array_key_exists($valueProperty, $valueData)) {
               if (is_numeric($this->idHasPart) && $valueData[$valueProperty]) {
                   $resp = $typeIsPartOfObject->get([ "id" => $valueData[$valueProperty] ]);
                   return $resp[0] ?? null;
               }
            }
            // many
            else {
                $this->table_has_table = $this->tableHasPart."_has_".$this->tableIsPartOf;
                // many to many
                if (self::table_exists($this->table_has_table)) {
                    $rel = $this->getRelationship($this->table, $this->idHasPart, $this->tableIsPartOf);
                    $data = null;
                    foreach ($rel as $valueRel) {
                       $data[] = $typeIsPartOfObject->schema($valueRel);
                    }
                    return $data;
                }
                // one to many
                else {
                    if ($typeIsPartOf == "Offer") {
                        $params = [ "itemOfferedType" => $this->tableHasPart, "itemOffered" => $this->idHasPart ];
                    } elseif ($typeIsPartOf == "Invoice" || $typeIsPartOf == "OrderItem") {
                        $params = [ "referencesOrder" => $this->idHasPart ];
                    } elseif ($typeIsPartOf == "WebPageElement") {
                        $params = [ "isPartOf" => $this->idHasPart ];
                    } else {
                        $params = [ $this->tableHasPart => $this->idHasPart ];
                    }
                    $data = $typeIsPartOfObject->get($params);
                    if (empty($data)) {
                        return null;
                    } else {
                        return $data;
                    }
                }
            }
        }
        return false;
    }*/
}
