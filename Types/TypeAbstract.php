<?php

namespace Fwc\Api\Type;

use Fwc\Api\Server\Crud;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class TypeAbstract extends Crud
{
    protected $request;
    protected $table;

    use SchemaTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    protected function get() 
    {
        // properties not exists
        $noWhere = [ "orderBy", "ordering", "limit", "groupBy", "offset", "id", "properties" ];
        
        $field = "*";
        $where = null;
        $groupBy = null;
        $orderBy = null;
        $limit = 200;
        $offset = null;
        $ordering = "unordering";
        $args = null;
        
        // params from URL queries strings
        $queryParams = $this->request->getQueryParams();
        
        // param id from argument url
        if ($this->request->getAttribute('id')) {
            $queryParams['id'] = $this->request->getAttribute('id');
        }
                
        if (!empty($queryParams)) {
            
            // set max limit
            $limit = isset($queryParams['limit']) && $queryParams['limit'] < 200 ? $queryParams['limit'] : $limit; 
            
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
        
        // properties
        if (isset($queryParams['properties'])) {
            $this->propertiesMerge($queryParams['properties']);
        }
        
        return $this->returnListAll( parent::read($field, $where, $groupBy, $orderBy, $limit, $offset, $args), $ordering );
    }
    
    private function returnListAll($data, $ordering)
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
    
    protected function post(array $params): array 
    {
        return parent::created($params);
    }

    protected function put(string $id): array
    {
        $params = $this->request->getParsedBody();
        
        $idname = "id".$this->table;
        
        $idvalue = $this->request->getAttribute('id');
        
        return parent::update($params, "`$idname`=$idvalue");        
    }

    protected function delete(string $id): array
    {
        $idname = 'id'.$this->table;
        return parent::erase([ $idname => $id ]);
    }

    protected function createSqlTable($type = null) 
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
