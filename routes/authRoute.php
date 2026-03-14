<?php

use Plinct\Api\Http\Controller\Authentication\AuthenticationController;
use Plinct\Api\Http\Controller\Authentication\AuthLoginController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	$route->group('auth/', function (RouteCollectorProxy $route) {

		// LOGIN
		$route->post('login', AuthLoginController::class)->setName('auth.login');

		// REGISTER
		$route->post('register', [AuthenticationController::class, 'register'])->setName('auth.register');

		// TOKEN VALIDATOR
		$route->get('tokenValidator', [AuthenticationController::class, 'isValidToken'])->setName('auth.tokenValidator');

		// RESET PASSWORD
		$route->post('reset_password', [AuthenticationController::class, 'resetPassword'])->setName('auth.resetPassword');

		// CHANGE PASSWORD
		$route->post('change_password', [AuthenticationController::class, 'changePassword'])->setName('auth.changePassword');
	});
};