<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;


return function(Route $route)
{
	$route->group('', function (Route $route) {
		/**
		 * GET
		 */
		$route->get('', function (Request $request, Response $response) {
			$data = RequestApi::server()->user()->httpRequest()->withPrivileges('r','user_admin')->get($request->getQueryParams());
			return ResponseApi::write($response, $data);
		});
		/**
		 * POST
		 */
		$route->post('', function (Request $request, response $response) {
			$data = RequestApi::server()->user()->httpRequest()->withPrivileges('c','user_admin')->post($request->getParsedBody());
			return ResponseApi::write($response, $data);
		});
		/**
		 * PUT
		 */
		$route->put('', function (Request $request, Response $response) {
			$data = RequestApi::server()->user()->httpRequest()->withPrivileges('u','user_admin')->put($request->getParsedBody());
			return ResponseApi::write($response, $data);
		});
		/**
		 * DELETE
		 */
		$route->delete('', function (Request $request, Response $response) {
			$data = RequestApi::server()->user()->httpRequest()->withPrivileges('d','user_admin')->delete($request->getQueryParams());
			return ResponseApi::write($response, $data);
		});
	});

	$route->group('/privileges', function(Route $route) {
		return RequestApi::routes()->userPrivileges($route);
	});
};
