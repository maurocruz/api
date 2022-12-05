<?php

declare(strict_types=1);

use Plinct\Api\Middleware\AuthMiddleware;
use Plinct\Api\Middleware\CorsMiddleware;
use Plinct\Api\Server\DatabaseAccess;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route)
{
	/**
	 * GET
	 */
	$route->get('/viewport', function (Request $request, Response $response, $args)
	{
		$db = new DatabaseAccess();
		$db->setTable('map_viewport')
			->setMethodRequest('get')
			->setParams($request->getQueryParams());

		$response->getBody()->write(json_encode($db->ready(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	});

	/**
	 * POST
	 */
	$route->post('/viewport', function (Request $request, Response $response)
	{
		$db = new DatabaseAccess();
		$db->setTable('map_viewport')
			->setMethodRequest('post')
			->setParams($request->getParsedBody());

		$response = $response->withHeader('Access-Control-Allow-Headers', 'Authorization');
		$response->getBody()->write(json_encode($db->ready(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	})->addMiddleware(new AuthMiddleware());

	/**
	 * PUT
	 */
	$route->put('/viewport', function (Request $request, Response $response)
	{
		$db = new DatabaseAccess();
		$db->setTable('map_viewport')
			->setMethodRequest('put')
			->setParams($request->getParsedBody());

		$response = $response->withHeader('Access-Control-Allow-Headers', 'Authorization');
		$response->getBody()->write(json_encode($db->ready(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	})->addMiddleware(new AuthMiddleware());

	/**
	 * DELETE
	 */
	$route->delete('/viewport', function (Request $request, Response $response)
	{
		$db = new DatabaseAccess();
		$db->setTable('map_viewport')
			->setMethodRequest('delete')
			->setParams($request->getQueryParams());

		$response = $response->withHeader('Access-Control-Allow-Headers', 'Authorization');
		$response->getBody()->write(json_encode($db->ready(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	})->addMiddleware(new AuthMiddleware());

	$route->options('[/{a}]', function (Request $request, Response $response, $args)
	{
		$status = 'fail'; $message = 'Nothing happened, OPTIONS request disabled.'; $data = null;

		$response->getBody()->write(json_encode(['status'=>$status,'message'=>$message,'data'=>$data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		return $response;
	})->addMiddleware(new CorsMiddleware([
		'Access-Control-Allow-Headers'=>'authorization'
	]));
};
