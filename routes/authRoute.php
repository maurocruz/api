<?php

use Plinct\Api\Http\Controller\Authentication\AuthLoginController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	$route->group('auth/', function (RouteCollectorProxy $route) {

		// LOGIN
		$route->post('login', AuthLoginController::class);

	});
};