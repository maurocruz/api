<?php
namespace fwc\Thing;
/**
 * PostalAddresModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
use fwc\Api\Crud;

class PostalAddressModel extends Crud {
    public $table = "postalAddress";
    
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM $this->table WHERE idPostalAddress=$id;";
        return parent::getQuery($query);
    }
}
