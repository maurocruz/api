<?php

declare(strict_types=1);

use Plinct\Api\Auth\AuthMiddleware;
use Plinct\Api\User\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

return function(Route $route)
{
	/**
	 * GET
	 */
	$route->get('', function (Request $request, Response $response)
	{
		$params = $request->getQueryParams() ?? null;
		$data = (new User())->get($params);
		$response = $response->withHeader('Access-Control-Allow-Headers', 'Authorization');
		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
		return $response;

	})->addMiddleware(new AuthMiddleware());

	/**
	 * POST
	 */
	$route->post('', function (Request $request, response $response)
	{
		$data = (new User())->post($request->getParsedBody());
		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	});

	/**
	 * PUT
	 */
	$route->put('', function (Request $request, Response $response)
	{
		$data = (new User())->put($request->getParsedBody());
		$response = $response->withHeader('Access-Control-Allow-Headers', 'Authorization');
		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;

	})->addMiddleware(new AuthMiddleware());

	/**
	 * DELETE
	 */
	$route->delete('', function (Request $request, Response $response)
	{
		$data = (new User())->delete($request->getQueryParams());
		$response = $response->withHeader('Access-Control-Allow-Headers', 'Authorization');
		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;

	})->addMiddleware(new AuthMiddleware());
};
