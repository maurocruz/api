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

return function(App $slimApp) {

    // get, post, put, delete
    /*
     * Init application
     */
    $slimApp->post('/api/start', function(Request $request, Response $response, $args) 
    {
        $username = $request->getParsedBody()['userDb'] ?? null;
        $password = $request->getParsedBody()['passDb'] ?? null;
        
        if ($username && $password) {            
            $driver = PDOConnect::getDrive();
            $host = PDOConnect::getHost();
            $dbname = PDOConnect::getDbname();
            
            PDOConnect::disconnect();
            
            $pdo = PDOConnect::connect($driver, $host, $dbname, $username, $password);
            
            if (array_key_exists('error', $pdo)) {
                $data = json_encode($pdo);
                
            } elseif (is_object($pdo)) { 
                $maintenance = new Maintenance($request);
                $data = json_encode($maintenance->start());
            }
            
        } else {
            $data = '{"data":"User and pass not found"}';
        }   
        
        $newResponse = $response->withHeader("Content-type", "'application/json'");
        
        $newResponse->getBody()->write($data);
        
        return $response;        
    });
    
    
    
    // POST login
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
        if ($request->getAttribute("userAuth") === false) {
            $data = [ "message" => "User not authorized" ];
            
        } else {        
            $type = $args['type'];
            $params = $request->getParsedBody();

            $className = "\\Fwc\\Api\\Type\\".ucfirst($type);

            if (class_exists($className)) {
                $typeClass = new $className($request);
                $data = $typeClass->post($params);
            }
        }
        
        $newResponse = $response->withHeader("Content-type", "'application/json'");        
        $newResponse->getBody()->write( json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );        
        return $response;
        
    })->add(function(Request $request, RequestHandler $handle)
    {
        if (Session::checkUserAdmin() === false) {
            $request = $request->withAttribute('userAuth', false);
            
        } else {     
            $request = $request->withAttribute('userAuth', true);   
        }
        
        $response = $handle->handle($request);     
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
        $dataController = new ApiController($request);
        
        $data = $dataController->getTypes($args);
        
        $newResponse = $response->withHeader("Content-type", "'application/json'");        
        $newResponse->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));        
        return $response;
    });
};

