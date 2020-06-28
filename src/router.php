<?php

namespace Fwc\Api;

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Fwc\Api\Server\PDOConnect;
use Fwc\Api\Server\Maintenance;
use Fwc\Api\Auth;
use Fwc\Api\Auth\Session;
use Fwc\Api\Auth\AuthMiddleware;

return function(App $slimApp) {

    // get, post, put, delete
    /*
     * Init application
     */
    $slimApp->post('/api/start', function(Request $request, Response $response, $args) 
    {
        $username = $request->getParsedBody()['username'] ?? null;
        $password = $request->getParsedBody()['password'] ?? null;
        
        if ($username && $password) {            
            $driver = PDOConnect::getDrive();
            $host = PDOConnect::getHost();
            $dbname = PDOConnect::getDbname();
            
            PDOConnect::disconnect();
            
            $pdo = PDOConnect::connect($driver, $host, $dbname, $username, $password);
            
            if (array_key_exists('error', $pdo)) {
                $data = $pdo;
                
            } elseif (is_object($pdo)) { 
                $maintenance = new Maintenance($request);
                $data = $maintenance->start();
            }
            
        } else {
            $data = [ "message" => "User and pass not found" ];
        }   
        
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        
        $response = $response->withHeader("Content-type", "'application/json'");
        return $response;        
    });
     
    
    // GET logout
    $slimApp->get('/api/logout', function(Request $request, Response $response)
    {
        $data = (new Auth\AuthController($request))->logout();
        
        $newResponse = $response->withHeader("Content-type", "'application/json'");        
        $newResponse->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));        
        return $response;
    });
    
    // Generic GET
    $slimApp->get('/api[/{type}[/{id}]]', function (Request $request, Response $response, $args) 
    {
        $type = $args['type'] ?? null;
        $id = $args['id'] ?? null;
        $params = $request->getQueryParams() ?? null;
        
        if ($type) {        
            $className = "\\Fwc\\Api\\Type\\".ucfirst($type);

            if (class_exists($className)) {
                $data = (new $className($request))->get($params);
                
            } else {
                 $data = [ "message" => "type not founded" ];
            }
            
        } else {
            $data = (new Type\index())->index();
        }
              
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));  
        
        //$response = $response->withHeader("Content-type", "'application/json'");        
        return $response;
    });
    
    
    /**
     * POST
     */
    $slimApp->post('/api/login', function (Request $request, Response $response) 
    {
        $auth = new Auth\AuthController($request);
        
        $data = $auth->login($request->getParsedBody());
        
        $newResponse = $response->withHeader("Content-type", "'application/json'");        
        $newResponse->getBody()->write(json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));        
        return $response;
    });
    
    // Generic POST
    $slimApp->post('/api/{type}', function(Request $request, Response $response, $args) 
    {
        $type = $args['type'];
        $params = $request->getParsedBody();
            
        if ($request->getAttribute("userAuth") === true) {
            
            PDOConnect::reconnectToAdmin();
            
            
            $className = "\\Fwc\\Api\\Type\\".ucfirst($type);

            if (class_exists($className)) {
                $typeClass = new $className($request);
                $data = $typeClass->post($params);
            }
            $response->getBody()->write(json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            
        } elseif ($type == "user") {
            unset($params['status']);
            $data = (new \Fwc\Api\Type\User($request))->post($params);
            $response->getBody()->write(json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } 
        
        $response = $response->withHeader("Content-type", "'application/json'");
        return $response;
        
    })->add(new AuthMiddleware());
    
       
    /**
     * DELETE
     */
    $slimApp->delete("/api/{type}/{id}", function (Request $request, Response $response, $args) 
    {
        if ($request->getAttribute("userAuth") === true) {
            
            PDOConnect::reconnectToAdmin();
            
            $classname = "\\Fwc\\Api\\Type\\".ucfirst($args['type']);
            
            $data = (new $classname($request))->delete($args['id']);
                           
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        }
        
        $response = $response->withHeader("Content-type", "application/json");
        return $response;
        
    })->addMiddleware(new AuthMiddleware());
    
    /**
     * PUT
     */
    $slimApp->put('/api/{type}/{id}', function (Request $request, Response $response, $args) 
    {
        if ($request->getAttribute('userAuth') === true) {
            
            $params = $request->getParsedBody() ?? null;
            
            PDOConnect::reconnectToAdmin();
                        
            $classname = "\\Fwc\\Api\\Type\\".ucfirst($args['type']);
            
            $data = json_encode((new $classname($request))->put($args['id'], $params), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );      
        
            $response->getBody()->write($data);
        }
        
        $response = $response->withHeader("Content-type", "'application/json'");        
        return $response;
        
    })->addMiddleware(new AuthMiddleware());    
};

