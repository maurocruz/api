<?php

declare(strict_types=1);

use Plinct\Api\Middleware\CorsMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

return function (Route $route)
{
	$route->options('',function (Request $request, Response $response) {
		return $response;
	})->addMiddleware(new CorsMiddleware([
		"Access-Control-Allow-Methods" => "OPTIONS,PUT,POST,GET,DELETE",
		"Access-Control-Allow-Headers" => "origin, x-requested-with, content-type, Authorization"
	]));

	$route->get('', function (Request $request, Response $response)
	{
		$params = $request->getQueryParams() ?? null;
		$format = $params['format'] ?? null;

		$placeData = ApiFactory::request()->type('place')->httpRequest()->setPermission()->get($params);

		//  CLASS HIERARCHY
		if ($format == "classHierarchy") {
			$data = ApiFactory::response()->format()->classHierarchy('place', $params)->ready();
		} elseif ($format == "geojson") {
			$data = ApiFactory::response()->format()->geojson($placeData)->ready();
		} else {
			$data = $placeData;
		}
		return ApiFactory::response()->write($response, $data);
	});
};