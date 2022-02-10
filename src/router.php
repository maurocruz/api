<?php

declare(strict_types=1);

namespace Plinct\Api;

use Plinct\Api\Server\Format\Format;
use Plinct\Api\Server\Search\Search;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;
use Plinct\Api\Auth;
use Plinct\Api\Auth\AuthMiddleware;
use Plinct\Api\Type\User;

return function(Route $route)
{
    $route->group('/api', function (Route $route)
    {
        /**
         * Init application
         */
        $route->post('/start', function(Request $request, Response $response)
        {
            $data = PlinctApi::starApplication($request->getParsedBody());
            $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
            return $response->withHeader("Content-type", "application/json");
        });

        /**
         * AUTHENTICATION
         */
        $route->group('/auth', function (Route $route)
        {
            $route->post('/reset_password', function (Request $request, Response $response)
            {
                $data = Auth\Authentication::resetPassword($request->getParsedBody());

                $response = $response->withHeader("Content-type", "'application/json'")
                    ->withHeader('Access-Control-Allow-Origin', '*')
                    ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                    ->withHeader('Access-Control-Allow-Headers', 'Content-Type');

                $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
                return $response;
            });

            $route->post('/change_password', function (Request $request, Response $response)
            {
               $data = Auth\Authentication::changePassword($request->getParsedBody());

               $newResponse = $response->withHeader("Content-type", "'application/json'")
                   ->withHeader('Access-Control-Allow-Origin', '*')
                   ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                   ->withHeader('Access-Control-Allow-Headers', 'Content-Type');

               $newResponse->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

               return $newResponse;
            });
       }) ;
    });


    /**
     * SEARCH
     */
    $route->get('/api/search', function(Request $request, Response $response)
    {
        $queryParams = $request->getQueryParams();

        if ($queryParams) {
            $data = (new Search())->getData($queryParams);
        } else {
            $data = ['message' => 'Plinct Search API'];
        }

        $response = $response->withHeader("Content-type", "application/json");
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));

        return $response;
    });

    /**
     * Generic GET
     */
    $route->get('/api[/{type}[/{id}]]', function (Request $request, Response $response, $args)
    {
        $type = $args['type'] ?? null;
        $params = $request->getQueryParams() ?? null;

        $format = $params['format'] ?? null;

        if ($type) {
            $className = "\\Plinct\\Api\\Type\\".ucfirst($type);

            if (class_exists($className)) {
                //  CLASS HIERARCHY
                if ($format == "classHierarchy") {
                    $data = (Format::classHierarchy($type, $params))->ready();
                }
                //
                else {
                    $data = (new $className())->get($params);
                }

            } else {
                 $data = [ "message" => "type not founded" ];
            }
        } else {
            $data = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);
        }

        $response = $response->withHeader("Content-type", "application/json");
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));

        return $response;
    });

    /**
     * LOGIN
     */
    $route->map(['OPTIONS','POST'],'/api/login', function (Request $request, Response $response)
    {
        //$data = Auth\Authentication::login($request->getParsedBody()); // ERRO COM CORS
        $data = (new Auth\AuthController())->login($request->getParsedBody());

        $newResponse = $response->withHeader("Content-type", "'application/json'")
            ->withHeader('Access-Control-Allow-Origin','*')
            ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type');

        $newResponse->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return $newResponse;
    });

    /**
     * REGISTER
     */
    $route->post('/api/register', function (Request $request, Response $response)
    {
        $data = (new Auth\AuthController())->register($request->getParsedBody());
        $newResponse = $response->withHeader("Content-type", "'application/json'");
        $newResponse->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $newResponse;
    });

    /**
     * POST
    */
    $route->post('/api/{type}', function(Request $request, Response $response, $args)
    {
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

        if($data) $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response->withHeader("Content-type", "application/json");

    })->addMiddleware(new AuthMiddleware());

    /**
     * PUT
     */
    $route->put('/api/{type}[/{id}]', function (Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody() ?? null;
        $params['id'] = $args['id'] ?? $params['id'] ?? $params['idIsPartOf'] ?? null;

        if (!$params['id']) {
            $data = [ "message" => "missing data (router.php on line 86)"];

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
        return $response->withHeader("Content-type", "application/json");

    })->addMiddleware(new AuthMiddleware());

    /**
     * DELETE
     */
    $route->delete("/api/{type}[/{id}]", function (Request $request, Response $response, $args)
    {
        $params = $request->getParsedBody() ?? null;
        $params['id'] = $args['id'] ?? $params['id'] ?? $params['idIsPartOf'] ?? null;
        $type = $args['type'];

        if ($response->getStatusCode() === 200) {
            if (!$params['id']) {
                $data = [ "message" => "missing data (router.php on delele - line 109)"];
            } else {
                $classname = "\\Plinct\\Api\\Type\\".ucfirst($type);
                $data = (new $classname())->delete($params);
            }            
        } else {            
            $data = null;
        }

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response;// $response->withHeader("Content-type", "application/json");

    })->addMiddleware(new AuthMiddleware()); 
};
