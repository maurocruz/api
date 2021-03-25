<?php
namespace Plinct\Api;

use Plinct\Api\Type\index;
use Plinct\Api\Type\User;
use Slim\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Plinct\Api\Auth;
use Plinct\Api\Auth\AuthMiddleware;

return function(App $slimApp) {
    /**
     * Init application
     */
    $slimApp->post('/api/start', function(Request $request, Response $response) {
        $data = PlinctApi::starApplication($request->getParsedBody());
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $response = $response->withHeader("Content-type", "application/json");
        return $response;        
    });
    
    /**
     * Generic GET
     */
    $slimApp->get('/api[/{type}[/{id}]]', function (Request $request, Response $response, $args) {
        $type = $args['type'] ?? null;
        $params = $request->getQueryParams() ?? null;
        if ($type) {        
            $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
            if (class_exists($className)) {
                $data = (new $className())->get($params);
            } else {
                 $data = [ "message" => "type not founded" ];
            }
        } else {
            $data = (new Index())->index();
        }
        $response = $response->withHeader("Content-type", "application/json");
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
        return $response;
    });
    /**
     * LOGIN
     */
    $slimApp->post('/api/login', function (Request $request, Response $response) {
        $data = (new Auth\AuthController())->token($request->getParsedBody());
        $newResponse = $response->withHeader("Content-type", "'application/json'");        
        $newResponse->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response;
    });
    /**
     * POST
    */
    $slimApp->post('/api/{type}', function(Request $request, Response $response, $args) {
        $type = $args['type'];
        $params = $request->getParsedBody();
        if ($response->getStatusCode() === 200) {
            $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
            $action = $request->getParsedBody()['action'] ?? null;
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
            $data = (new User())->post($params);
        } else {
            $data = null;
        }
        $data ? $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) : null;
        $response = $response->withHeader("Content-type", "application/json");
        return $response;
    })->addMiddleware(new AuthMiddleware());
    /**
     * PUT
     */
    $slimApp->put('/api/{type}[/{id}]', function (Request $request, Response $response, $args) {
        $data = null;
        $params = $request->getParsedBody() ?? null;
        $params['id'] = $args['id'] ?? $params['id'] ?? null;
        if (!$params['id']) {
            $data = [ "message" => "missing data"];
        } elseif ($response->getStatusCode() === 200) {
            $className = "\\Plinct\\Api\\Type\\".ucfirst($args['type']);
            if (class_exists($className)) {
                $data = (new $className())->put($params);
            } else {
                $data = [ "message" => "Type not founded" ];                
            }
        } else {
            $data = [ "message" => "Unauthorized" ];
        }
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
        $response = $response->withHeader("Content-type", "application/json");
        return $response;
    })->addMiddleware(new AuthMiddleware());
    /**
     * DELETE
     */
    $slimApp->delete("/api/{type}[/{id}]", function (Request $request, Response $response, $args) {
        $params = $request->getParsedBody() ?? null;
        $params['id'] = $args['id'] ?? $params['id'] ?? null;
        $type = $args['type'];
        if ($response->getStatusCode() === 200) {
            if (!$params['id']) {
                $data = [ "message" => "missing data"];
            } else {
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
