<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Model;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class TypeAbstract extends Model 
{
    protected $request;
    protected $table;


    use SchemaTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function index(string $where = null, $orderBy = null, $groupBy = null, $limit = null, $offset = null) 
    {
        // params from URL queries strings
        $queryParams = $this->request->getQueryParams();
        
        // param id from argument url
        if ($this->request->getAttribute('id')) {
            $queryParams['id'] = $this->request->getAttribute('id');
        }
        
        // set max limit
        $limit = $limit && $limit < 200 ? $limit : 200; 
        
        // fields not exists
        $noWhere = [ "orderBy", "ordering", "limit", "groupBy", "offset", "id" ];
                
        if (!empty($queryParams)) {
            
            foreach ($queryParams as $key => $value) {
                
                // ORDER BY
                if (stripos($key, "orderBy") !== false) {
                    $ordering = $queryParams['ordering'] ?? 'ASC';
                    $orderBy = stripos($ordering, 'rand') !== false ? "rand()" : $value." ".$ordering;
                }
                
                // WHERE
                $like = stristr($key,"like", true);
                if ($like) {
                    $whereArray[] = "`$like` LIKE '%$value%'";
                    
                } elseif (!in_array($key, $noWhere)) {
                    $whereArray[] = "`$key`='$value'"; 
                    
                } elseif (stripos($key, "id") !== false) {
                    $idname = "id".$this->table;
                    $whereArray[] = "`$idname`=$value";
                }
                
                // LIMIT
                if (stripos($key, 'limit') !== false) {
                    $limit = $value;
                }
            }
        }        
        
        // WHERE
        $where = isset($whereArray) ? implode(" AND ", $whereArray) : null;
        
        return $this->returnListAll( parent::index($where, $orderBy, $groupBy, $limit, $offset), $ordering ?? null );
    }
    
    protected function returnListAll($data, $ordering)
    {
        if (isset($data['error'])) {
           return $data; 
           
        } elseif (empty ($data)) {
            return $this->listItem();
            
        } else {
            foreach ($data as $key => $value) { 
               $list[] = $this->schema($value);
            }
            
            return $this->listItem($list, $ordering);
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
