<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;

class Invoice extends Entity implements TypeInterface {
    protected $table = "invoice";
    protected $type = "Invoice";
    protected $properties = [ "*" ];
    protected $hasTypes = [ "referencesOrder" => "Order", "customer" => true, "provider" => true ];

    public function get(array $params): array {
        if (array_key_exists('orderBy',$params) === false) {
            $params['orderBy'] = "paymentDueDate DESC";
        }
        return parent::get($params);
    }

    public function createSqlTable($type = null): array {
        // sql create statement
        $message[] = parent::createSqlTable("Invoice");
        return $message;
    }
}
