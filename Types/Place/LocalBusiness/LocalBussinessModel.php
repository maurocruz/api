<?php

namespace fwc\Thing;

/**
 * ProductModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class LocalBusinessModel extends Crud {
    protected $table = "localBusiness";
        
    public function getAll($search = null, $order = "name ASC", $limit = null, $offset = null) {
        $query = "SELECT * FROM $this->table";
        $query .= $search ? " WHERE name LIKE '%$search%'" : null;
        $query .= " ORDER BY $order";
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= $offset ? " OFFSET $offset" : null;
        $query .= ";";
        
        return parent::getQuery($query);
    }

    public function getById($identifier, $search = null) {
        $query = "SELECT * FROM $this->table WHERE id{$this->table}=$identifier";
        $query .= $search ? " AND name LIKE '%$search%'" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getAllByLimitOffset($limit,$offset, $order = "name ASC") {
        $query = "SELECT * FROM $this->table ORDER BY $order LIMIT $limit OFFSET $offset;";
        return parent::getQuery($query);
    }
    
    public function getAllWithCustomer($order = null) {
        $query = "SELECT * FROM $this->table";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
}
