<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class History extends Entity implements TypeInterface {
    protected string $table = "history";
    protected string $type = "History";
    protected array $properties = [ "*" ];
    protected array $withTypes = [];
    
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
