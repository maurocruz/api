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
        $filterget = new FilterGet($this->request->getQueryParams(), $this->table, $this->properties);
        
        $this->properties = $filterget->getProperties();
                
        $data = parent::read($filterget->field(), $filterget->where(), $filterget->groupBy(), $filterget->orderBy(), $filterget->limit(), $filterget->offset());
        
        return $this->listSchema($data);
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
