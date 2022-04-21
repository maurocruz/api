<?php

declare(strict_types=1);

use Plinct\Api\Auth\AuthController;
use Plinct\Api\Auth\Authentication;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route)
{
  /**
   * LOGIN
   */
  $route->map(['OPTIONS','POST'],'/login', function (Request $request, Response $response)
  {
    //$data = Authentication::login($request->getParsedBody()); // ERRO COM CORS
    $data = (new AuthController())->login($request->getParsedBody());

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    return $response;
  });

	/**
	 * REGISTER
	 */
	$route->map(['OPTIONS','POST'], '/register', function (Request $request, Response $response)
	{
		//$data = Authentication::register($request->getParsedBody());
		$data = (new AuthController())->register($request->getParsedBody());

		$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
		return $response;
	});

	/**
	 * RESET PASSWORD
	 */
  $route->post('/reset_password', function (Request $request, Response $response)
  {
    $data = Authentication::resetPassword($request->getParsedBody());

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    return $response;
  });

	/**
	 * CHANGE PASSWORD
	 */
  $route->post('/change_password', function (Request $request, Response $response)
  {
    $data = Authentication::changePassword($request->getParsedBody());

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

    return $response;
  });
};
