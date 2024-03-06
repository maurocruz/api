<?php
declare(strict_types=1);

use Plinct\Api\Middleware\AuthMiddleware;
use Plinct\Api\Middleware\CorsMiddleware;
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
	$route->get('', function (Request $request, Response $response, $args)
	{
		$type = $args['type'] ?? null;
		$params = $request->getQueryParams() ?? null;
		if ($type) {
			$dataRequest = ApiFactory::request()->type($type)->get($params)->ready();
			$data = ApiFactory::response()->type($type)->setData($dataRequest)->ready();
		} else {
			$data = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);
		}
		return ApiFactory::response()->write($response, $data);
	});

	/**
	 * POST
	 */
	$route->post('', function(Request $request, Response $response, $args)
	{
		$type = $args['type'] ?? null;
		$params = $request->getParsedBody();
		$uploadedFiles = $_FILES;
		$data = ApiFactory::request()->type($type)->post($params, $uploadedFiles)->ready();
		return ApiFactory::response()->write($response, $data);
	})->addMiddleware(new AuthMiddleware());

	/**
	 * PUT
	 */
	$route->put('', function (Request $request, Response $response, $args) {
		$type = $args['type'];
		$params = $request->getParsedBody() ?? null;
		$data = ApiFactory::request()->type($type)->put($params)->ready();
		return ApiFactory::response()->write($response, $data);
	})->addMiddleware(new AuthMiddleware());

	/**
	 * DELETE
	 */
	$route->delete("[/{id}]", function (Request $request, Response $response, $args)
	{
		$type = $args['type'];
		$params = $request->getQueryParams() ?? null;
		$data = ApiFactory::request()->type($type)->delete($params)->ready();
		return ApiFactory::response()->write($response, $data);
	})->addMiddleware(new AuthMiddleware());
};
