<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Model;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class TypeAbstract extends Model 
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null) 
    {
        return $this->returnListAll( parent::index($where, $orderBy, $groupBy, $limit, $offset) );
    }
    
    protected function returnListAll($data)
    {
        if (isset($data['error'])) {
           return json_encode($data); 
           
        } elseif (empty ($data)) {
            return json_encode(ItemList::list(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
        } else {
            foreach ($data as $key => $value) {                
                $list[] = $this->schema($value);
            }
            
            return json_encode(ItemList::list(count($data),$list), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);     
        }
    }
    
    public function createSqlTable($type = null) 
    {
        $dir = realpath(__DIR__ . "/../Types/" . ucfirst($type));
               
        $sqlFile = $dir."/createSqlTable.sql";
        
        if (file_exists($sqlFile)) {
            
            parent::getQuery(file_get_contents($sqlFile));
            
            return true;
           
        } else {
            return false;
        }
    }
}
