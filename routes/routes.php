<?php

use Plinct\Api\Http\Controller\HomeController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route) {
	// HOME
	$route->group('/api/', function (RouteCollectorProxy $route) {
		// HOME
		$route->get('', HomeController::class);
		// AUTH
		(require __DIR__ . '/authRoute.php')($route);
		// USER
		(require __DIR__ . '/userRoute.php')($route);
		// MODULES
		(require_once __DIR__ . '/modulesRoute.php')($route);
	});
};