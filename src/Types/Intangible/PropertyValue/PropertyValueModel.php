<?php
namespace fwc\Thing;
/**
 * PropertyValueModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
class PropertyValueModel extends \fwc\Api\Crud {
    public $table = "attributes";
    
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    public function getPropertyValueIsPartOf($tableOwner, $idOwner) {
        return parent::getHasTable($tableOwner, $idOwner);
    }
    
    public function addNewPropertyValueWithPartOf($tableOwner, $idOwner, $data) {            
        parent::insert($data);
        $idattributes = parent::lastInsertId();        
        parent::insertWithPartOf($tableOwner, $idOwner, $this->table, $idattributes);
    }
    
    public function update($data, $where) {
        parent::update($data, $where);
    }
}
