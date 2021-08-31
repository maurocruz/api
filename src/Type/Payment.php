<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use ReflectionException;

class Payment extends Entity implements TypeInterface
{
    /**
     * @var string
     */
    protected $table = "payment";
    /**
     * @var string
     */
    protected string $type = "Payment";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*" ];
    /**
     * @var array|string[]
     */
    protected array $hasTypes = [ "referencesOrder" => "Order" ];

    /**
     * @param array $params
     * @return array
     */
    public function get(array $params): array
    {
        if (array_key_exists('orderBy',$params) === false) {
            $params['orderBy'] = "paymentDate";
        }

        return parent::get($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function post(array $params): array
    {
        $params = self::setHistory("CREATE", $params);
        return parent::post($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function put(array $params): array
    {
        $params = self::setHistory("UPDATE", $params);
        unset($params['tableHasPart']);
        unset($params['idHasPart']);
        unset($params['idadvertising']);
        return parent::put($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function delete(array $params): array
    {
        $params = self::setHistory("DELETE", $params);
        return parent::delete([ "idpayment" => $params['id'] ]);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
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
