<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Auth\SessionUser;


class History extends Entity implements TypeInterface {
    protected $table = "history";
    protected $type = "History";
    protected $properties = [ "*" ];
    protected $withTypes = [  ];

    public function get(array $params): array {
        return parent::get($params);
    }
    
    public function post(array $params): array {
        return parent::post($params);
    }
    
    public function put(array $params): array {
        return parent::put($params);
    }
    
    public function delete(array $params): array {
        return parent::delete($params);
    }
    
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
