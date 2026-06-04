<?php

use Plinct\Api\Http\Controller\User\UserController;
use Plinct\Api\Http\Controller\User\UserPrivilegesController;
use Plinct\Api\Http\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	$route->group('user', function (RouteCollectorProxy $route) {

		$route->get('', [UserController::class,'read'])->setName('user.read');
		$route->put('', [UserController::class,'update'])->setName('user.update');
		$route->delete('', [UserController::class,'delete'])->setName('user.delete');

		// PRIVILEGES
		$route->group('/privileges', function(RouteCollectorProxy $route)
		{
			$route->get('', [UserPrivilegesController::class,'read'])->setName('user.privileges.read');
			$route->post('', [UserPrivilegesController::class,'create'])->setName('user.privileges.create');
			$route->put('', [UserPrivilegesController::class, 'update'])->setName('user.privileges.update');
			$route->delete('', [UserPrivilegesController::class, 'delete'])->setName('user.privileges.delete');
		});

	})->add(AuthMiddleware::class);
};