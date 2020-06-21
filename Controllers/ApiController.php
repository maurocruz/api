<?php

namespace Fwc\Api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Fwc\Api\Types;

class ApiController
{
    protected $request;
    
    public function __construct(Request $request) 
    {
        $this->request = $request;
    }
    
    public function getTypes($args)
    {
        $type = $args['type'] ?? "Thing";
        /*$id = $args['id'] ?? null;
        $queryParams = $this->request->getQueryParams();
        $parsedBody = $this->request->getParsedBody();
        
        $tableOwner = $queryParams['tableOwner'] ?? null;
        $idOwner = $queryParams['idOwner'] ?? null;
        $orderBy = $queryParams['orderBy'] ?? null;
        $groupBy = $queryParams['groupBy'] ?? null;
        $limit = $queryParams['limit'] ?? null;
        $offset = $queryParams['offset'] ?? null;
        $name = $queryParams['name'] ?? null;
        $keywords = $queryParams['keywords'] ?? null;*/

        $nameClass = "\\Fwc\\Api\\Type\\".ucfirst($type);
        
        if (class_exists($nameClass)) {
            $typeObject = new $nameClass($this->request);
            return $typeObject->get($args);
        } else {
            return [ "message" => "Not founded type '$type'" ];
        }
            
        /*if ($id) {

        } elseif ($tableOwner && $idOwner) {
            return $typeObject->getHasPart($tableOwner, $idOwner, $orderBy, $groupby);
            
        } else {            
            $where = $name ? "`name` LIKE '%{$name}%'" : null;
            $where = $keywords ? "`keywords` LIKE '%{$keywords}%'" : $where;
            return $typeObject->index($where, $orderBy, $groupBy, $limit, $offset);
        }*/
    }
    
   /* public function getContent($args) 
    {
        $queryStrings = $this->request->getQueryParams();
        $type = $args['type'] ?? $queryStrings['type'] ?? null;
        $q = $queryStrings['q'] ?? null;
        $orderby = $queryStrings['orderby'] ?? null;
        $propertyName = $queryStrings['propertyName'] ?? 'name';
        $fwc_id = $queryStrings['fwc_id'] ?? null;
        
        if ($type) {
            $where = $q ?  "`$propertyName` LIKE '%$q%'" : null;
            $order = $orderby;
            $limit = 10;
            $offset = null;
            $class = \fwc\Cms\Helper\ClassFactory::createFwcThingClass($type, $settings);
            if ($class) {
                if ($fwc_id) {
                    return ($class)->selectById($fwc_id);
                } else {
                    $data = ($class)->listAll($where, $order, $limit, $offset);
                    if ($data) {
                        return $data;
                    } else {
                        return '{ "data": null }';
                    }
                }
            } else {
                return '{ "error": "Type '.$type.' not exists" }';
            }
        } else {
            return '{ "data": null }';
        } 
    }*/
}
