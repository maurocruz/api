<?php
namespace fwc\Thing;
/**
 * PlaceModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

use fwc\Api\Crud;

class PlaceModel extends Crud {
    public $table = "place";
    
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    public function listAll($search = null, $order = null) {
        $query = "SELECT * FROM $this->table";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function selectPlaceById($id) {
        $query = "SELECT * from $this->table WHERE id{$this->table} = $id;";
        return parent::getQuery($query);
    } 
    
    public function selectByName($words) {
        $query = "SELECT * FROM $this->table WHERE placeName LIKE '$words%' GROUP BY $this->table.idplace ORDER BY placeName;";
        return parent::getQuery($query);
    }
    
    public function addPlaceIntoTable($tableOwner, $idOwner, $idplace) {
        $this->table = $tableOwner;        
        return parent::update([ "location"=>$idplace ], "id$tableOwner=$idOwner");
    }
}
