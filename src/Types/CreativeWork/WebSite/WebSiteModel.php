<?php
namespace fwc\Thing;
/**
 * WebSiteModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */
use fwc\Api\Crud;

class WebSiteModel extends Crud {
    public $table = "webSite";

    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
    
    public function install() {        
        $query = file_get_contents(__DIR__.'/SQLCreateTables.sql');
        return parent::getQuery($query);
    }
}
