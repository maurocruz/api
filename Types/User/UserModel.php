<?php
namespace fwc\Thing;

/**
 * UserGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class UserModel extends Crud {
    public $table = "user";
        
    public function __construct() {
        parent::__construct();               
    }
    
    // list person
    public function listAll($search = null, $limit = null, $order = null ) {
        $query = "SELECT * FROM $this->table";
        $query .= $order ? " ORDER BY $order" : " ORDER BY name ASC";
        $query .= $limit ? " LIMIT $limit" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function getUserById($id) {
        $query = "SELECT * FROM $this->table WHERE iduser=$id";
        return parent::getQuery($query);
    }

    public function email_exists($email) {
        $data = $this->checkEmailExists($email);                
        if ($data[0]['q'] > 0) {
            return true;
        } else {
            return false;
        }
    } 
    
    // CHECA SE EXISTE EMAIL
    public function checkEmailExists($email) {
        $query = "SELECT COUNT(email) as q FROM $this->table WHERE email='$email';";
        return parent::getQuery($query);
    }
    
    // OBTEM DADOS POR EMAIL
    public function getDataByEmail($email) {
        $query = "SELECT * FROM $this->table WHERE email = '{$email}';";        
        return parent::getQuery($query);
    }
    
    // ADICIONA NOVO USUARIO
    public function addNewUser($dados){
        parent::created($dados);
        return parent::lastInsertId();
    }
}
