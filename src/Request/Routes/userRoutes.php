<?php

declare(strict_types=1);

use Plinct\Api\Middleware\CorsMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

return function(Route $route)
{
	$route->group('', function (Route $route)
	{
		$route->options('', function (Request $request, Response $response) {
				return $response;
		})->addMiddleware(new CorsMiddleware([
			'Access-Control-Allow-Headers' => 'origin, x-requested-with, content-type, Authorization'
		]));
		/**
		 * GET
		 */
		$route->get('', function (Request $request, Response $response) {
			$data = ApiFactory::server()->user()->httpRequest()->setPermission()->get($request->getQueryParams());
			return ApiFactory::response()->write($response, $data);
		});

		/**
		 * POST
		 */
		$route->post('', function (Request $request, response $response) {
			$data = ApiFactory::server()->user()->httpRequest()->withPrivileges('c','user_admin')->post($request->getParsedBody());
			return ApiFactory::response()->write($response, $data);
		});

		/**
		 * PUT
		 */
		$route->put('', function (Request $request, Response $response)
		{
			$params = $request->getParsedBody();
			$httpRequest = ApiFactory::server()->user()->httpRequest();
			if (isset($params['iduser'])) {
				if ($params['iduser'] == ApiFactory::user()->userLogged()->getIduser()) {
					$data = $httpRequest->setPermission()->put($params);
				} else {
					$data = $httpRequest->withPrivileges('u', 'user_admin')->put($params);
				}
			} else {
				$data = ApiFactory::response()->message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
			}
			return ApiFactory::response()->write($response, $data);
		});

		/**
		 * DELETE
		 */
		$route->delete('', function (Request $request, Response $response) {
			$data = ApiFactory::server()->user()->httpRequest()->withPrivileges('d','user_admin')->delete($request->getQueryParams());
			return ApiFactory::response()->write($response, $data);
		});
	});

	$route->group('/privileges', function(Route $route) {
		return ApiFactory::request()->routes()->userPrivileges($route);
	});
};
