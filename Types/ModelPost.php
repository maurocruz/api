<?php

namespace fwc\Thing;

/**
 * ModelPost
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

abstract class ModelPost extends Crud implements ModelPostInterface {
    protected $settings;
    protected $tableOwner;
    protected $idOwner;
    protected $POST;
    protected $FILE;
    protected $idWebPage;

    public function __construct() 
    {
        parent::__construct();
        $this->POST = filter_input_array(INPUT_POST) ?? $_POST ?? null;        
        $this->FILE = filter_var_array($_FILES) ?? $_FILES ?? null;        
        $this->tableOwner = $this->POST['tableOwner'] ?? null;
        unset($this->POST['tableOwner']);        
        $this->idOwner = $this->POST['idOwner'] ?? null;
        unset($this->POST['idOwner']);         
        unset($this->POST['submit']);
        unset($this->POST['submit_x']);
        unset($this->POST['submit_y']);
        unset($this->POST['MAX_FILE_SIZE']);        
        $this->idWebPage = isset($this->POST['idwebPage']) ? (int) $this->POST['idwebPage'] : null;
        unset($this->POST['idwebPage']);        
    }
    
    protected function updateById($idname = null) {
        $idname = $idname ?? "id".$this->table;
        $id = $this->POST[$idname];
        unset($this->POST[$idname]);
        parent::update($this->POST, "`$idname`=$id");
    }
    
    private function setTableHas($tableOwner, $tableHas) {
        $this->table = $tableOwner."_has_".$tableHas;
    }

    public function createNewAndReturnLastId($data): string {
        $this->created($data);
        return $this->lastInsertId();
    }
    
    protected function count(string $where){
        $data = parent::read("COUNT(*) AS q", $where);
        return $data[0]['q'];
    }


    public function insertNewWithPartOf(string $tableOwner, int $idOwner, array $data) {
        $idHas = $this->createNewAndReturnLastId($data);
        return $this->insertWithHasPart($tableOwner, $idOwner, $this->table, $idHas);
    }
    
    /**
     * Insert new table_has_table with additional data, if exists
     * 
     * @param string $tableOwner
     * @param int $idOwner
     * @param string $tableHas
     * @param int $idHas
     * @param array $data
     * @return type
     */
    public function insertWithHasPart(string $tableOwner, int $idOwner, string $tableHas, int $idHas, array $data = null) {
        $this->setTableHas($tableOwner, $tableHas);
        $attributes = ["id$tableOwner" => $idOwner, "id$tableHas" => $idHas ];
        $attr = $data ? array_merge($attributes, $data) : $attributes;
        return $this->createNewAndReturnLastId($attr);
    }
    
    public function editHasPart(string $tableOwner, int $idOwner, string $tableHas, int $idHas, array $data = null) {
        $this->setTableHas($tableOwner, $tableHas);
        $idOwnerName = "id".$tableOwner;
        $idHasName = "id".$tableHas;
        return parent::update($data, "`$idOwnerName`=$idOwner AND `$idHasName`=$idHas");        
    }
    
    public function deleteWithHasPart(string $tableOwner, int $idOwner, string $tableHas, int $idHas): object {
        $this->setTableHas($tableOwner, $tableHas);
        return parent::delete(["id$tableOwner" => $idOwner, "id$tableHas" => $idHas ]);
    }
    
    protected function saveJsonPageWeb() {       
        if ($this->idWebPage) {
            (new WebPagePost($this->settings))->saveJsonWebPage($this->idWebPage);
        }
    } 
    
    public function createSqlTable($type = null) 
    {
        $dir = \fwc\Helper\FileSystemHelper::searchDirOnDir(__DIR__, $type);
               
        $sqlFile = $dir."/createSqlTable.sql";
        
        if (file_exists($sqlFile) && $this->connect->query(file_get_contents($sqlFile))) {
           return true;
        } else {
            return false;
        }
    }
    
    protected function history($actionDescription) {
        $table = $this->table;
        $this->table = "history";
        $idhistory = $this->createNewAndReturnLastId([ "actionDescription" => $actionDescription] );
        $this->table = $table;
        return $idhistory;
    }
}
