<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class History extends Entity implements TypeInterface {
    protected $table = "history";
    protected $type = "History";
    protected $properties = [ "*" ];
    protected $withTypes = [];
    
    public function createSqlTable($type = null): array {
        return parent::createSqlTable("History");
    }

    /**
     * SET Params
     * @param $action
     * @param $summary
     * @param $tableHasPart
     * @param $idHasPart
     * @return array
     */
    public function postHistory($action, $summary, $tableHasPart, $idHasPart): array {
        $paramsHistory["action"] = $action;
        $paramsHistory["summary"] = $summary;
        $paramsHistory['tableHasPart'] = $tableHasPart;
        $paramsHistory['idHasPart'] = $idHasPart;
        $paramsHistory['user'] = $_SESSION['userLogin']['name'];
        return $this->post($paramsHistory);
    }
}
