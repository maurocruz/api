<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Payment extends Entity implements TypeInterface {
    protected string $table = "payment";
    protected string $type = "Payment";
    protected array $properties = [ "*" ];
    protected array $hasTypes = [ "referencesOrder" => "Order" ];

    public function get(array $params): array {
        if (array_key_exists('orderBy',$params) === false) {
            $params['orderBy'] = "paymentDate";
        }
        return parent::get($params);
    }

    public function post(array $params): array {
        $params = self::setHistory("CREATE", $params);
        return parent::post($params);
    }

    public function put(array $params): array {
        $params = self::setHistory("UPDATE", $params);
        unset($params['tableHasPart']);
        unset($params['idHasPart']);
        unset($params['idadvertising']);
        return parent::put($params);
    }

    public function delete(array $params): array {
        $params = self::setHistory("DELETE", $params);
        return parent::delete([ "idpayment" => $params['id'] ]);
    }

    public function createSqlTable($type = null): array {
        return parent::createSqlTable("Payment");
    }

    /**
     * SET HISTORY
     * @param string $action
     * @param array $params
     * @return array
     */
    private static function setHistory(string $action, array $params): array {
        $paramsHistory["action"] = $action;
        $paramsHistory["summary"] = "Valor: ".number_format($params['valorparc'], 2, ",", ".")." / vencimento: ".$params['vencimentoparc']." / quitado: ".$params['quitado'];
        $paramsHistory['tableHasPart'] = $params['tableHasPart'];
        $paramsHistory['idHasPart'] = $params['idHasPart'];
        $paramsHistory['user'] = $_SESSION['userLogin']['name'];
        (new History())->post($paramsHistory);
        return $params;
    }
}
