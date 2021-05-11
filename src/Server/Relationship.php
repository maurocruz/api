<?php
namespace Plinct\Api\Server;

use Plinct\PDO\PDOConnect;
use Plinct\PDO\Crud;

class Relationship extends Crud {
    protected $tableHasPart;
    protected $idHasPart;
    protected $tableIsPartOf;
    protected $idIsPartOf;
    protected $table_has_table;
    private $params;
    protected $table;
    protected $type;
    protected $properties;
    protected $hasTypes;

    public function setParams($params): void {
        $this->params = $params;
    }

    public function getParams() {
        return $this->params;
    }

    public function setVars($params) {
        if ($params['tableHasPart']) { 
            $this->tableHasPart = lcfirst($params['tableHasPart']) ?? "";
            $this->idHasPart = $params['idHasPart'] ?? null;
            $this->tableIsPartOf = $params['tableIsPartOf'] ?? $this->table;
            $this->idIsPartOf = $params['idIsPartOf'] ?? $params['id'] ?? null;
            $this->table_has_table = $this->tableHasPart.'_has_'.$this->tableIsPartOf;
            unset($params['tableHasPart']);
            unset($params['idHasPart']);
            unset($params['tableIsPartOf']);
            unset($params['idIsPartOf']);
            unset($params['id']);
        }
        $this->params = $params;
    }
    
    public function getRelationship($tableHasPart, $idHasPart, $tableIsPartOf, $params = null): array {
        $filterget = new FilterGet($params, $this->table, $this->properties ?? []);
        $orderBy = $filterget->orderBy();
        $tableRel = $tableHasPart.'_has_'.$tableIsPartOf;
        $idHasPartName = 'id'.$tableHasPart;
        $idIsPartOfName = 'id'.$tableIsPartOf;
        $query = "SELECT * FROM `$tableIsPartOf`, `$tableRel` WHERE `$tableIsPartOf`.$idIsPartOfName=`$tableRel`.$idIsPartOfName AND `$tableRel`.$idHasPartName=$idHasPart";
        // representativeOfPage
        $query .= in_array('representativeOfPage',$this->properties) ? " AND `representativeOfPage` IS TRUE " : null;
        // IMAGE OBJECT
        $query .= $tableIsPartOf == "imageObject" ? " ORDER BY position ASC" : ($orderBy ? " ORDER BY $orderBy" : null);
        // CONTACT POINT
        $query .= $tableIsPartOf == "contactPoint" ? " ORDER BY position ASC" : ($orderBy ? " ORDER BY $orderBy" : null);
        // HISTORY
        $query .= $tableIsPartOf == "history" ? " ORDER BY datetime DESC" : ($orderBy ? " ORDER BY $orderBy" : null);
        $query .= ";";
        return PDOConnect::run($query);
    }
    
    public function postRelationship(array $params): array {
        $this->setVars($params);
        if (!$this->idIsPartOf) {
            $data = parent::created($this->params);
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
        // with manys relationship type
        else {                         
            $idHasPartName = array_key_exists($this->tableHasPart,$this->hasTypes) ? $this->tableHasPart : 'id'.$this->tableHasPart;
            $idIsPartOfName = 'id'.$this->tableIsPartOf;
            // many to many
            if (self::table_exists($this->table_has_table)) {
                $this->table = $this->table_has_table;
                $paramCreate = [ $idHasPartName => $this->idHasPart, $idIsPartOfName => $this->idIsPartOf ];
               return parent::created($paramCreate);
            }
            // many to many
            else {
                $this->table = $this->tableIsPartOf;
                $params[$idHasPartName] = $params['idHasPart'];
                unset($params['tableHasPart']);
                unset($params['idHasPart']);
                return parent::created($params);
            }
        }
        return $this->params;
    }
    
    public function putRelationship($params): array {
        $this->setVars($params);
        $this->table = $this->table_has_table;
        $idHasPartName = 'id'.$this->tableHasPart;
        $idIsPartOfName = 'id'.$this->tableIsPartOf;
        $where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";
        return parent::update($this->params, $where);
    }

    public function deleteRelationship($params): array {
        $this->setVars($params);
        $this->table = $this->table_has_table;
        $idHasPartName = 'id'.$this->tableHasPart;
        $idIsPartOfName = 'id'.$this->tableIsPartOf;
        $where = "`$idHasPartName`=$this->idHasPart AND `$idIsPartOfName` = $this->idIsPartOf";
        return parent::erase($where);
    }
    
    public function getHasTypes(): array {
        return $this->hasTypes;
    }       
    
    private static function getTypeObject($type): ?object {
        $classname = "\\Plinct\\Api\\Type\\". ucfirst($type);
        if (class_exists($classname)) {
            return new $classname();
        }
        return null;
    }
    
    private static function table_exists($table): bool {
        return !empty(PDOConnect::run("SHOW tables like '$table';"));
    }
    
    private function propertyIsPartOf() {
        $propColumns = [];
        // check which properties exists in table
        $columns = PDOConnect::run("SHOW COLUMNS FROM `$this->tableHasPart`");
        // fields columns bd
        foreach ($columns as $valueColumns) {
            $propColumns[] = $valueColumns['Field'];
        }
        // has types of table has part
        $hasTypes = (self::getTypeObject($this->tableHasPart))->getHasTypes();
        // property is part type
        $propIsPartType = array_keys($hasTypes, $this->type, true);
        return in_array($propIsPartType[0], $propColumns) ? $propIsPartType[0] : null;
    }
    
    protected function relationshipsInSchema($valueData, $valueProperty) {
        $typeIsPartOf = $this->hasTypes[$valueProperty] === true ? $valueData[$valueProperty.'Type'] : $this->hasTypes[$valueProperty];
        $this->tableIsPartOf = lcfirst($typeIsPartOf);
        $typeIsPartOfObject = self::getTypeObject($typeIsPartOf);
        if ($typeIsPartOfObject) {
            // one to one
            if (array_key_exists($valueProperty, $valueData)) {
               if (is_numeric($this->idHasPart) && $valueData[$valueProperty]) {
                   $resp = $typeIsPartOfObject->get([ "id" => $valueData[$valueProperty] ]);
                   return $resp[0] ?? null;
               }
            }
            // manys
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
                    } else {
                        $params = [ $this->tableHasPart => $this->idHasPart ];
                    }
                    $data = $typeIsPartOfObject->get(array_merge($params, [ "limit" => "none" ]));
                    if (empty($data)) {
                        return null;
                    } else {
                        return $data;
                    }
                }
            }
        }
        return false;
    }
}
