<?php

/**
 * HistoryGet
 *
 * @author Mauro Cruz <maurocruz@pirenopolis.tur.br>
 */

namespace fwc\Thing;

class HistoryGet extends ModelGet
{
    public $table = "history";
    
    public function getHistoryWithPartOf($tableOwner, $idOwner, $order = null) {
        $data = (new HistoryModel())->getHistoryWithPartOf($tableOwner, $idOwner, $order);
        return json_encode($data);
    }
    
}
