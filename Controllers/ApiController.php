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
        $nameClass = "\\Fwc\\Api\\Type\\".ucfirst($type);
        
        if (class_exists($nameClass)) {
            $typeObject = new $nameClass($this->request);
            return $typeObject->get($args);
        } else {
            return [ "message" => "Not founded type '$type'" ];
        }           
    }    
}
