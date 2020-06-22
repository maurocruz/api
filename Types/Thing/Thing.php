<?php

namespace Fwc\Api\Type;

class Thing extends TypeAbstract implements TypeInterface
{
    protected $table = "thing";
    protected $type = "Thing";

    // index, show (SELECT)
    public function get($args): array
    {
        return parent::index();
    }
    
    // create (INSERT)
    public function post(array $args): array
    {        
    }
    
    // update (UPDATE)
    public function put(string $id): array
    {
        
    }
    
    // delete (DELETE)
    public function erase(string $id): array
    {
        
    }
    
    public function createSqlTable($type = null): bool
    {
        return parent::createSqlTable('thing');
    }
    
    


    /*public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null) {
        $data = parent::index($where, $orderBy, $groupBy, $limit, $offset);
    }

    public function listAll(string $where = null, $order = null, $limit = null, $offset = null) {
        parent::listAll($where, $order, $limit, $offset);
    }
    
    public function selectById($id, $order = null, $field = '*') {
        parent::selectById($id, $order, $field);
    }
    
    public function index() 
    {     
        $data = parent::getQuery("SELECT TABLE_NAME AS type , TABLE_ROWS AS items FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '{$this->dbname}' AND TABLE_NAME NOT LIKE '%_has_%';");        
        return json_encode($data);
    }
    
    public function show() {
        
    }*/
}

