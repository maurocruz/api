<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Type;

use Plinct\Api\Request\Server\Entity;
use ReflectionException;

class Invoice extends Entity
{
    /**
     * @var string
     */
    protected string $table = "invoice";
    /**
     * @var string
     */
    protected string $type = "Invoice";
    /**
     * @var array|string[]
     */
    protected array $properties = [ "*" ];
    /**
     * @var array
     */
    protected array $hasTypes = [ "referencesOrder" => "Order", "customer" => true, "provider" => true ];

    /**
     * @param array $params
     * @return array
     */
    public function get(array $params = []): array
    {
        if (array_key_exists('orderBy',$params) === false) {
            $params['orderBy'] = "paymentDueDate DESC";
        }

        return parent::get($params);
    }

    /**
     * @param null $type
     * @return array
     * @throws ReflectionException
     */
    public function createSqlTable($type = null): array
    {
        $message[] = parent::createSqlTable("Invoice");
        return $message;
    }
}
