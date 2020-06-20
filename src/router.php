<?php

namespace Fwc\Api;

use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Fwc\Api\Server\PDOConnect;
use Fwc\Api\Server\Maintenance;

return function(App $slimApp) {

    // get, post, put, delete

    $slimApp->get('/api[/{type}[/{id}]]', function (Request $request, Response $response, $args) 
    {
        $dataController = new ApiController($request);
        
        $data = $dataController->getTypes($args);
        
        $newResponse = $response->withHeader("Content-type", "'application/json'");
        
        $newResponse->getBody()->write($data);
        
        return $response;
    });
    
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
};

