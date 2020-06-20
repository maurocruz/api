<?php

namespace Fwc\Api\Server;

abstract class Model extends Crud
{        
    // index
    protected function index(string $where = null, $order = null, $groupBy = null, $limit = null, $offset = null)
    {
        $query = "SELECT * FROM $this->table";
        $query .= $where ? " WHERE $where" : null;
        $query .= $groupBy ? " GROUP BY $groupBy" : null;
        $query .= $order ? " ORDER BY $order" : null;
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= $offset ? " OFFSET $offset" : null;
        $query .= ";";
        return parent::getQuery($query);
    } 
}
