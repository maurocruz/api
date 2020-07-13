<?php

/*
 * HISTORY
 */

namespace fwc\Thing;

use fwc\Api\Crud;

class HistoryModel extends Crud 
{
    public $table = 'history';
    
    public function setHistory($action, $summary, $idOwner, $tableOwner) {
        parent::created([ "action" => $action, "summary" => $summary, "user" => \fwc\helpers\UsersHelper::getIdusers() ] );
        $idhistory = parent::lastInsertId();        
        $this->table = $tableOwner."_has_history";        
        parent::created(["id$tableOwner" => $idOwner, "idhistory" => $idhistory]);
    }
    
    public function getHistoryWithPartOf($tableOwner, $idOwner, $order = null) {
        return parent::getHasTable($tableOwner, $idOwner, $order);
    }
}

