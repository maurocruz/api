<?php

use Plinct\Api\Http\Controller\Entities\Thing\Product\ProductController;
use Plinct\Api\Http\Controller\Entities\Thing\Product\ProductReadController;
use Plinct\Api\Http\Controller\Modules\CreativeWork\MediaObject\MediaObjectReadController;
use Plinct\Api\Http\Controller\Modules\CreativeWork\MediaObject\MediaObjectCreateController;
use Plinct\Api\Http\Controller\Modules\ModuleActionsController;
use Plinct\Api\Http\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	$route->group('', function (RouteCollectorProxy $route)
	{
		// MEDIA OBJECT
		$route->get('mediaObject', MediaObjectReadController::class)->setName('mediaObject.read');
		$route->post('mediaObject', MediaObjectCreateController::class)->setName('mediaObject.create');

		$route->post('product', [ProductController::class, 'create'])->setName('product.create');
		$route->delete('product', [ProductController::class, 'delete'])->setName('product.delete');

		// ACTIONS
		$route->post('{type}',[ModuleActionsController::class, 'create'])->setName('modules.create');
		$route->put('{type}',[ModuleActionsController::class, 'update'])->setName('modules.update');
		$route->delete('{type}',[ModuleActionsController::class, 'delete'])->setName('modules.delete');

	})->add(AuthMiddleware::class);

	// PRODUCT
	$route->get('product', ProductReadController::class)->setName('product.read');
};