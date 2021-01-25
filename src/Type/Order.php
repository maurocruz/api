<?php

namespace Plinct\Api\Type;

use Plinct\Api\Server\Entity;
use Plinct\Api\Server\Maintenance;
use Plinct\Api\Server\PDOConnect;

class Order extends Entity implements TypeInterface
{
    protected $table = "order";
    
    protected $type = "Order";
    
    protected $properties = [ "*" ];
    
    protected $hasTypes = [ "history" => "History", "partOfInvoice" => "Invoice", "orderedItem" => "OrderItem", "customer" => true, "seller" => true ];

    public function get(array $params): array 
    {
        return parent::get($params);
    }
    
    public function post(array $params): array 
    {        
        $data = parent::post($params);
        
        (new History())->postHistory("CREATED", _("Create new order"), "advertising", $data['id']);
        
        return $data;
    }
    
    public function put($params): array 
    {
        (new History())->postHistory("UPDATE", filter_input(INPUT_GET, "summaryHistory"), "order", $params['id']);
        
        return parent::put($params);
    }
    
    public function delete(array $params): array 
    {        
        return parent::delete($params);
    }

    public function search($params): array
    {
        $nameLike = $params['nameLike'];
        unset($params['nameLike']);
        $query = "select * from `order` left join `localBusiness` on `order`.customer=localBusiness.idlocalBusiness AND `order`.customerType='LocalBusiness' left join `organization` on `order`.customer=`organization`.idorganization AND `order`.customerType='Organization' left join `person` on `order`.customer=`person`.idperson AND `order`.customerType='Person' where (`localBusiness`.`name` like '%$nameLike%' OR `organization`.`name` like '%$nameLike%' OR `person`.`name` like '%$nameLike%') GROUP BY `order`.customer;";
        $data = PDOConnect::run($query);
        return $this->buildSchema($params,$data);
    }
    
    public function createSqlTable($type = null): array
    {
        $maintenance = new Maintenance();
        $message[] = $maintenance->createSqlTable("Invoice");
        $message[] = parent::createSqlTable("Order");
        $message[] = $maintenance->createSqlTable("OrderItem");
        return $message;
    }
}