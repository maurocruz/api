<?php

namespace fwc\Thing;

/**
 * WebPageModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class WebPageModel extends Crud {
    public $table = "webPage";
     
    public function __construct() {
        parent::__construct();
    }
    
    public function countAll() {
        $query = "SELECT COUNT(*) as q FROM $this->table;";
        $data = parent::getQuery($query);
        return $data[0]['q'] ?? 0;
    }
    
    public function getAllPages() {
        $query = "SELECT * FROM $this->table ORDER BY url ASC;";        
        return parent::getQuery($query);
    }
    
    public function getByUrl($url) { // DEPRECATE used in breadcrmblist line 23 
        $query = "SELECT * FROM $this->table WHERE url = '$url';";
        return parent::getQuery($query);
    }
    
    public function getJsonByUrl($url) {
        $query = "SELECT jsonwebpage FROM $this->table WHERE url='$url';";
        $data = parent::getQuery($query);
        return empty($data) ? null : $data[0]['jsonwebpage'];
    }
    
    public function insertNewWebpage($data) {
        parent::insert($data);
        return parent::lastInsertId();
    }
}
