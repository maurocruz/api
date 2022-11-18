<?php

declare(strict_types=1);

namespace Plinct\Api;

use Plinct\Api\Middleware\CorsMiddleware;
use Plinct\Api\Server\Search\Search;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\Middleware\AuthMiddleware;

return function(Route $route)
{
  $route->group('/api', function (Route $route)
  {
		/** Init application */
    $route->post('/start', function(Request $request, Response $response) {
      $data = PlinctApi::starApplication($request->getParsedBody());
      return $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    });

	  /**
	   * AUTHENTICATION
	   */
	  $route->group('/auth', function (Route $route) {
			return ApiFactory::request()->routes()->auth($route);
	  });

	  /**
	   * USER
	   */
		$route->group('/user', function(Route $route) {
			return ApiFactory::request()->routes()->user($route);
		})->addMiddleware(new AuthMiddleware());

	  /**
	   * SEARCH
	   */
	  $route->get('/search', function(Request $request, Response $response)
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
		 * MAP
		 */
	  $route->group('/map', function (Route $route)	{
			$mapRoutes = require __DIR__ . '/mapRoutes.php';
			return $mapRoutes($route);
	  });

	  /**
	   * PLACE
	   */
		$route->group('/place', function (Route $route) {
			$placeRoutes = require __DIR__.'/placeRoutes.php';
			return $placeRoutes($route);
		});

	  /**
	   * TYPE
	   */
		$route->group('/{type}', function (Route $route) {
			$typeRoutes = require __DIR__.'/typeRoutes.php';
			return $typeRoutes($route);
		});

		// HOME
		$route->get('', function(Request $request, Response $response) {
			ApiFactory::response()->write($response, ['status'=>'success', 'message'=>'Welcome to Plinct API']);
			return $response;
		});

  })->addMiddleware(new CorsMiddleware([
		"Content-type"=>"application/json",
	  "Access-Control-Allow-Origin"=>"*"
  ]));

  /**
   * DELETE
   */
  $route->delete("/api/{type}[/{id}]", function (Request $request, Response $response, $args)
  {
	  $type = $args['type'];
	  $params = $request->getParsedBody() ?? $request->getQueryParams() ?? null;
	  $params["id$type"] = $args['id'] ?? $params['id'] ?? $params['idIsPartOf'] ?? $params["id$type"] ?? null;
		unset($params['id']);

    if ($response->getStatusCode() === 200) {
      if (!$params["id$type"]) {
        $data = [ "message" => "missing data (".__FILE__." on ".__LINE__.")"];
      } else {
        $classname = "\\Plinct\\Api\\Type\\".ucfirst($type);
        $data = (new $classname())->delete($params);
      }
    } else {
      $data = null;
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    return $response;

  })->addMiddleware(new AuthMiddleware());
};
