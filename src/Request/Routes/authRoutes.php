<?php

declare(strict_types=1);

use Plinct\Api\Middleware\CorsMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

return function (Route $route)
{
  /**
   * LOGIN
   */
	$route->group('/login', function (Route $route) {
		$route->options('', function (Request $request, Response $response) {
			return $response;
		});
		$route->post('', function (Request $request, Response $response) {
			$data = ApiFactory::server()->user()->authentication()->login($request->getParsedBody());
			return ApiFactory::response()->write($response, $data);
		});
	})->addMiddleware(new CorsMiddleware([
		'Access-Control-Allow-Headers' => 'origin, x-requested-with, content-type, Authorization'
	]));

	/**
	 * REGISTER
	 */
	$route->group('/register', function(Route $route) {
		$route->options('', function (Request $request, Response $response) {
			return $response;
		});
		$route->post('', function (Request $request, Response $response) {
			$data = ApiFactory::server()->user()->authentication()->register($request->getParsedBody());
			return ApiFactory::response()->write($response, $data);
		});
	});

	/**
	 * RESET PASSWORD
	 */
	$route->options('/reset_password', fn(Request $request, Response $response) => $response);

  $route->post('/reset_password', function (Request $request, Response $response)
  {
		$data = ApiFactory::server()->user()->authentication()->resetPassword($request->getParsedBody());
		return ApiFactory::response()->write($response, $data);
  });


	/**
	 * CHANGE PASSWORD
	 */
  $route->post('/change_password', function (Request $request, Response $response)
  {
		$data = ApiFactory::server()->user()->authentication()->changePassword($request->getParsedBody());
		return ApiFactory::response()->write($response, $data);
  });
};
