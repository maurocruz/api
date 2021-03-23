<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Invoice extends Entity implements TypeInterface {
    protected string $table = "invoice";
    protected string $type = "Invoice";
    protected array $properties = [ "*" ];
    protected array $hasTypes = [ "referencesOrder" => "Order", "customer" => true, "provider" => true ];

    public function get(array $params): array {
        if (array_key_exists('orderBy',$params) === false) {
            $params['orderBy'] = "paymentDueDate DESC";
        }
        return parent::get($params);
    }

    public function post(array $params): array {
        self::setHistory("CREATE", $params);
        unset($params['tableHasPart']);
        return parent::post($params);
    }

    public function put(array $params): array {
        self::setHistory("UPDATE", $params);
        return parent::put($params);
    }

    public function delete(array $params): array {
        self::setHistory("DELETE", $params);
        return parent::delete($params);
    }

    public function createSqlTable($type = null): array {
        // sql create statement
        $message[] = parent::createSqlTable("Invoice");
        return $message;
    }

    /**
     * SET HISTORY
     * @param string $action
     * @param array $params
     * @return array
     */
    private static function setHistory(string $action, array $params): array {
        $summary = "Valor: ".number_format($params['totalPaymentDue'], 2, ",", ".")." / vencimento: ".$params['paymentDueDate']." / quitado: ".$params['paymentDate'];
        $tableHasPart = $params['tableHasPart'];
        $idHasPart = $params['referencesOrder'];
        return (new History())->postHistory($action, $summary, $tableHasPart, $idHasPart);
    }
}
