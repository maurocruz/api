<?php
namespace fwc\Thing;

/**
 * WebPageElementModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

class WebPageElementModel extends \fwc\Api\Crud {
    public $table = "webPageElement";
        
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    public function getByIdWebPage($idWebPage, $order = null) {
        $query = "SELECT * FROM $this->table where idwebPage=$idWebPage";
        $query .= $order ? " ORDER BY $order" : null;
        $query .= ";";
        return parent::getQuery($query);
    }
    
    public function addNew($idwebPage, $data) {
        $data['idwebPage'] = $idwebPage;
        parent::insert($data);
        return parent::lastInsertId();
    }
}
