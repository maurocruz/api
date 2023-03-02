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
			$typeClass = ApiFactory::server()->type($type);
			$data = $typeClass->exists()
				? $typeClass->httpRequest()->setPermission()->get($params)
				: ApiFactory::response()->message()->fail()->thisTypeNotExists();
		} else {
			$data = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);
		}

		return ApiFactory::response()->write($response, $data);
	});

	/**
	 * PUT
	 */
	$route->put('', function (Request $request, Response $response, $args)
	{
		$type = $args['type'];
		$params = $request->getParsedBody() ?? null;
		$id = $params['id'] ?? $params['idHasPart'] ?? $params["id". lcfirst($type)] ?? null;

		if (!$id) {
			$data = ApiFactory::response()->message()->fail()->inputDataIsMissing(["params"=>$params]);
		}
		elseif ($response->getStatusCode() === 200) {
			$typeClass = ApiFactory::server()->type($type);

			if($typeClass->exists()) {
				$data = ApiFactory::server()->type($type)->httpRequest()->withPrivileges('u', $type, 2)->put($params);
			} else {
				$data = ApiFactory::response()->message()->fail()->thisTypeNotExists();
			}
		}
		else {
			$data = ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction();
		}

		return ApiFactory::response()->write($response, $data);

	})->addMiddleware(new AuthMiddleware());

	/**
	 * POST
	 */
	$route->post('', function(Request $request, Response $response, $args)
	{
		$type = $args['type'] ?? null;
		$params = $request->getParsedBody();
		$uploadedFiles = $_FILES;
		$action = $request->getParsedBody()['action'] ?? null;
		// NAMESPACE
		$namespace = $params['tableHasPart'] ?? $type;
		if($action == 'create') {
			$data = ApiFactory::server()->type($type)->create();
		} else {
			$data = ApiFactory::server()->type($type)->httpRequest()->withPrivileges('c', $namespace, 2)->post($params, $uploadedFiles);
		}
		return ApiFactory::response()->write($response, $data);
	})->addMiddleware(new AuthMiddleware());

	/**
	 * DELETE
	 */
	$route->delete("[/{id}]", function (Request $request, Response $response, $args)
	{
		$type = $args['type'];
		$params = $request->getParsedBody() ?? $request->getQueryParams() ?? null;
		$params["id$type"] = $args['id'] ?? $params['id'] ?? $params['idIsPartOf'] ?? $params["id$type"] ?? null;
		unset($params['id']);

		if ($response->getStatusCode() === 200) {
			if (!$params["id$type"]) {
				$data = ApiFactory::response()->message()->fail()->inputDataIsMissing($params);
			} else {
				$data = ApiFactory::server()->type($type)->httpRequest()->withPrivileges('d', $type, 2)->delete($params);
			}
		} else {
			$data = null;
		}
		return ApiFactory::response()->write($response, $data);

	})->addMiddleware(new AuthMiddleware());
};
