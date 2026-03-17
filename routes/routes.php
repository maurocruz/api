<?php

use Plinct\Api\Http\Controller\HomeController;
use Plinct\Api\Http\Middleware\PermissioMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route) {
	// HOME
	$route->group('/api/', function (RouteCollectorProxy $route) {

		$route->options('{routes:.+}', function ($request, $response) {
			return $response;
		});

		// HOME
		$route->get('', HomeController::class);

		// AUTH
		(require __DIR__ . '/authRoute.php')($route);

		// USER
		(require __DIR__ . '/userRoute.php')($route);

		// MODULES
		(require_once __DIR__ . '/entityRoutes.php')($route);

		// CONFIGURATION
		(require_once __DIR__ . '/configRoute.php')($route);
	});
};