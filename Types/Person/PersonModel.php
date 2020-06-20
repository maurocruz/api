<?php
namespace fwc\Thing;

/**
 * PersonModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class PersonModel extends Crud {
    public $table = "person";
    
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    // list person
    public function listPersons($search = null, $limit = null, $order = null ) {
        $query = "SELECT * FROM $this->table";
        $query .= " WHERE givenName LIKE '%$search%' OR familyName LIKE '%$search%' OR additionalName LIKE '%$search%'";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getPersonById($id) {
        $query = "SELECT * from $this->table WHERE idperson=$id;";
        return parent::getQuery($query);
    }
    
    /*public function selectByWord($word) {
        if(strlen($word) > 1) {
            $query = "(SELECT idperson, name FROM person WHERE name LIKE '$word%')";
            if (strlen($word) > 3) {
                $query .= " UNION ";
                $query .= "(SELECT idperson, name FROM person WHERE name LIKE '%$word%')";
            }
            $query .= ";";
            return parent::getQuery($query);  
        } else {
            return false;
        }     
    }*/
}
