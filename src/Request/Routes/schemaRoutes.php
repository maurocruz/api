<?php

declare(strict_types=1);

use Plinct\Api\Middleware\CorsMiddleware;
use Plinct\Api\Schema\Schema;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

return function(Route $route)
{
	$route->options('',function (Request $request, Response $response) {
		return $response;
	})->addMiddleware(new CorsMiddleware([
		"Access-Control-Allow-Methods" => "OPTIONS,PUT,POST,GET,DELETE",
		"Access-Control-Allow-Headers" => "origin, x-requested-with, content-type, Authorization"
	]));

	/**
	 * Generic GET
	 */
	$route->get('[/{class}]', function (Request $request, Response $response, $args)
	{
		$params = $request->getQueryParams();
		$class = $args['class'] ?? $params['class'] ?? null;
		$schemas = $params['schemas'] ?? null;
		$subClass = $params['subClass'] ?? null;
		$schema = new Schema();
		$schema->setClass($class);
		$schema->setSchemas($schemas);
		$schema->setSubClass($subClass);
		return ApiFactory::response()->write($response, $schema->ready() );
	});
};
