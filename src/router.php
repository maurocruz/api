<?php

namespace Plinct\Api;

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Plinct\Api\Server\PDOConnect;
use Plinct\Api\Server\Maintenance;
use Plinct\Api\Auth;
use Plinct\Api\Auth\Session;
use Plinct\Api\Auth\AuthMiddleware;

return function(App $slimApp) 
{
    session_start();
        
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
        
        $response = $response->withHeader("Content-type", "application/json");
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
            $className = "\\Plinct\\Api\\Type\\".ucfirst($type);

            if (class_exists($className)) {
                $data = (new $className($request))->get($params);
                
            } else {
                 $data = [ "message" => "type not founded" ];
            }
            
        } else {
            $data = (new Type\index())->index();
        }

        $response = $response->withHeader("Content-type", "application/json");

        $response = $response->withHeader('Access-Control-Allow-Origin', '*');

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));  
              
        
        return $response;
    });
    
    
    /**
     * LOGIN
     */
    $slimApp->post('/api/login', function (Request $request, Response $response) 
    {
        $auth = new Auth\AuthController();
        
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
        $className = "\\Plinct\\Api\\Type\\".ucfirst($type);      
        $action = $request->getParsedBody()['action'] ?? null;
            
        if ($request->getAttribute("userAuth") === true) {
            
            PDOConnect::reconnectToAdmin();    

            if (class_exists($className)) {
                
                $classObject = new $className();
                
                if ($action == 'create') {            
                    $data = $classObject->createSqlTable();                
                } else {
                    $data = $classObject->post($params);
                }
                
            } else {
                $data = [ "message" => "Type not founded" ];
            }            
            
        } elseif ($type == "user") {
            unset($params['status']);
            $data = (new \Plinct\Api\Type\User($request))->post($params);
            
        } 
        
        $response->getBody()->write(json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        
        $response = $response->withHeader("Content-type", "application/json");
        
        return $response;
        
    })->add(new AuthMiddleware());
    
    
    /**
     * PUT
     */
    $slimApp->put('/api/{type}[/{id}]', function (Request $request, Response $response, $args) 
    {
        $params = $request->getParsedBody() ?? null;
        $params['id'] = $args['id'] ?? $params['id'] ?? null;
        $className = "\\Plinct\\Api\\Type\\".ucfirst($args['type']);
        
        if (!$params['id']) {
            $data = [ "message" => "missing data"];
            
        } elseif ($request->getAttribute('userAuth') === true) {            
            
            PDOConnect::reconnectToAdmin();
                  
            if (class_exists($className)) {
                $data = (new $className())->put($params);
                
            } else {
                $data = [ "message" => "Type not founded" ];                
            }        
        }
        
        $response->getBody()->write(json_encode($data), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
            
        $response = $response->withHeader("Content-type", "application/json");        
        
        return $response;
        
    })->addMiddleware(new AuthMiddleware());   
    
    /**
     * DELETE
     */
    $slimApp->delete("/api/{type}[/{id}]", function (Request $request, Response $response, $args) 
    {
        $params = $request->getParsedBody() ?? null;
        $params['id'] = $args['id'] ?? $params['id'] ?? null;
        $type = $args['type'];
        
        if ($request->getAttribute("userAuth") === true) {
            if (!$params['id']) {
            $data = [ "message" => "missing data"];
            
            } else {
                PDOConnect::reconnectToAdmin();

                $classname = "\\Plinct\\Api\\Type\\".ucfirst($type);

                $data = (new $classname())->delete($params);
            }            
        } else {            
            $data = null;
        }
        
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        
        $response = $response->withHeader("Content-type", "application/json");        
        
        return $response;
        
    })->addMiddleware(new AuthMiddleware()); 
};

