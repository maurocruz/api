<?php

namespace Plinct\Api\Auth;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): ResponseInterface 
    {
        if (Session::checkUserAdmin() === false) {
            $request = $request->withAttribute('userAuth', false);
            
            $data =  json_encode([ "message" => "User not authorized" ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            
            $response = $handler->handle($request);
            
            $response->getBody()->write($data);   
                        
        } else {     
            $request = $request->withAttribute('userAuth', true); 
            $response = $handler->handle($request);    
        }
           
        return $response;
    }   
}
