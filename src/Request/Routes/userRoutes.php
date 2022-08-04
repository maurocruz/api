<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\User\User;
use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;


return function(Route $route)
{
	$route->group('', function (Route $route) {
		/**
		 * GET
		 */
		$route->get('', function (Request $request, Response $response) {
			$data = RequestApi::server()->user()->httpRequest()->get($request->getQueryParams());
			return ResponseApi::write($response, $data);
		});
		/**
		 * POST
		 */
		$route->post('', function (Request $request, response $response) {
			$data = RequestApi::server()->user()->httpRequest()->post($request->getParsedBody());
			return ResponseApi::write($response, $data);
		});
		/**
		 * PUT
		 */
		$route->put('', function (Request $request, Response $response)
		{
			if (User::userLogged()->havePermission(3,'u')) {
				$data = RequestApi::server()->user()->httpRequest()->put($request->getParsedBody());
			} else {
				$data = ResponseApi::message()->fail()->userNotAuthorizedForThisAction();
			}

			$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
			return $response;
		});
		/**
		 * DELETE
		 */
		$route->delete('', function (Request $request, Response $response) {
			$data = RequestApi::server()->user()->httpRequest()->delete($request->getQueryParams());
			return ResponseApi::write($response, $data);
		});
	});

	$route->group('/permission', function(Route $route) {
		return RequestApi::routes()->userPermissions($route);
	});
};
