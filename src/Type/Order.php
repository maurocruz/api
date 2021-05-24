<?php
namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\FilterGet;
use Plinct\Api\Server\Maintenance;
use Plinct\PDO\PDOConnect;

class Order extends Entity implements TypeInterface {
    protected $table = "order";
    protected $type = "Order";
    protected $properties = [ "*" ];
    protected $hasTypes = [ "history" => "History", "partOfInvoice" => "Invoice", "orderedItem" => "OrderItem", "customer" => true, "seller" => true ];

    public function search($params, $nameLike): array {
        $orderBy = $params['orderBy'];
        $query = "select `order`.* from `order` left join `localBusiness` on `order`.customer=localBusiness.idlocalBusiness AND `order`.customerType='LocalBusiness' left join `organization` on `order`.customer=`organization`.idorganization AND `order`.customerType='Organization' left join `person` on `order`.customer=`person`.idperson AND `order`.customerType='Person' where (`localBusiness`.`name` like '%$nameLike%' OR `organization`.`name` like '%$nameLike%' OR `person`.`name` like '%$nameLike%') order by $orderBy;";
        $data = PDOConnect::run($query);
        $filterGet = new FilterGet($params, $this->table, $this->properties);
        $this->properties = $filterGet->getProperties();
        return $this->buildSchema($params,$data);
    }
    
    public function createSqlTable($type = null): array {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("Invoice");
        $message[] = parent::createSqlTable("Order");
        $message[] = $maintenance->createSqlTable("OrderItem");
        return $message;
    }
}
