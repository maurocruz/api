<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;

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
			$data = RequestApi::server()->user()->authentication()->login($request->getParsedBody());
			return ResponseApi::write($response, $data);
		});
	});

	/**
	 * REGISTER
	 */
	$route->group('/register', function(Route $route) {
		$route->options('', function (Request $request, Response $response) {
			return $response;
		});
		$route->post('', function (Request $request, Response $response) {
			$data = RequestApi::server()->user()->httpRequest()->post($request->getParsedBody());
			return ResponseApi::write($response, $data);
		});
	});

	/**
	 * RESET PASSWORD
	 */
	$route->options('/reset_password', fn(Request $request, Response $response) => $response);

  $route->post('/reset_password', function (Request $request, Response $response)
  {
		$data = RequestApi::server()->user()->authentication()->resetPassword($request->getParsedBody());
		return ResponseApi::write($response, $data);
  });


	/**
	 * CHANGE PASSWORD
	 */
  $route->post('/change_password', function (Request $request, Response $response)
  {
		$data = RequestApi::server()->user()->authentication()->changePassword($request->getParsedBody());
		return ResponseApi::write($response, $data);
  });
};
