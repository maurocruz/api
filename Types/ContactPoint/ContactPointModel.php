<?php
namespace fwc\Thing;

/**
 * ContactPointModel
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

class ContactPointModel extends \fwc\Api\Crud {
    public $table = "contactPoint";
    
    public function __construct($settings) {
        parent::__construct($settings['pdo']);
    }
}
