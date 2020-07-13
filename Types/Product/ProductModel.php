<?php

namespace fwc\Thing;

/**
 * ProductModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class ProductModel extends \fwc\Api\Crud
{
    public $table = "product";
    
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    public function getAll($order = null)
    {
        $query = "SELECT * FROM $this->table";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getProductById($id, $order = null)
    {
        $query = "SELECT * FROM $this->table WHERE idproduct=$id";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getProdutosByAdditionalType($additionalType, $order = null)
    {
        $query = "SELECT * FROM $this->table WHERE type LIKE '%$additionalType%'";
        // order
        $query .= $order ? " ORDER BY $order" : null;        
        $query .= ";";
        
        return parent::getQuery($query);
    }
    
    public function getProductInStockByAdditionalType($additionalType, $order = null)
    {
        $query = "SELECT * FROM $this->table WHERE additionalType LIKE '%$additionalType%' AND availability='InStock'";
        // order
        $query .= $order ? " ORDER BY $order" : null;        
        $query .= ";";
        
        return parent::getQuery($query);
    }


    public function insert(array $data) 
    {
        parent::insert($data);
        return parent::lastInsertId();
    }
}
