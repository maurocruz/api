<?php

use Plinct\Api\Http\Controller\Config\ConfigController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	$route->group('config', function (RouteCollectorProxy $route) {
		// INSTALL MODULE
		$route->post('/installModule', [ConfigController::class,'installModule'])->setName('config.installModule');

		// UPDATE
		$route->get('/update', [ConfigController::class,'update'])->setName('config.update');
	});
};
