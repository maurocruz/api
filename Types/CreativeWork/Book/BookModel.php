<?php

namespace fwc\Thing;

/**
 * BookModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class BookModel extends Crud
{
    public$table = "book";
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll($order = "ASC")
    {
        $query = "SELECT * FROM $this->table ORDER BY name $order;";
        return parent::getQuery($query);
    }
}
