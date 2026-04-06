<?php

use Plinct\Api\Http\Controller\Entities\Action\ActionReadController;
use Plinct\Api\Http\Controller\Entities\EntityReadController;
use Plinct\Api\Http\Controller\Entities\EntityUpdateController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\CreativeWorkController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\ImageObject\ImageObjectController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\MediaObjectCreateController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\MediaObjectReadController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPage\WebPageController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPageElement\WebPageElementController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebSite\WebSiteController;
use Plinct\Api\Http\Controller\Entities\Thing\Product\ProductController;
use Plinct\Api\Http\Controller\Entities\Thing\Product\ProductReadController;
use Plinct\Api\Http\Controller\Modules\ModuleActionsController;
use Plinct\Api\Http\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	// ACTION
	$route->get('action', ActionReadController::class)->setName('action.read');

	// CREATIVE WORK
	$route->get('creativeWork', [CreativeWorkController::class, 'read'])->setName('creativeWork.read');

	// IMAGE OBJECT
	$route->get('imageObject', [ImageObjectController::class, 'read'])->setName('imageObject.read');

	// MEDIA OBJECT
	$route->get('mediaObject', MediaObjectReadController::class)->setName('mediaObject.read');

	// PRODUCT
	$route->get('product', ProductReadController::class)->setName('product.read');

	// WEB PAGE
	$route->get('webPage', [WebPageController::class, 'read'])->setName('webPage.read');

	// WEB PAGE ELEMENT
	$route->get('webPageElement', [WebPageElementController::class, 'read'])->setName('webPageElement.read');

	// WEB SITE
	$route->get('webSite', [WebSiteController::class, 'read'])->setName('webSite.read');

	// DEFAULT
	$route->get('{type}',[EntityReadController::class, 'read'])->setName('modules.read');


	// METHODS ACTIONS
	$route->group('', function (RouteCollectorProxy $route)
	{
		// IMAGE OBJECT
		$route->post('imageObject', [ImageObjectController::class, 'create'])->setName('imageObject.create');

		// MEDIA OBJECT
		$route->post('mediaObject', MediaObjectCreateController::class)->setName('mediaObject.create');

		// PRODUCT
		$route->post('product', [ProductController::class, 'create'])->setName('product.create');
		$route->delete('product', [ProductController::class, 'delete'])->setName('product.delete');

		// WEB PAGE
		$route->post('webPage', [WebPageController::class, 'create'])->setName('webPage.create');

		// WEB PAGE ELEMENT
		$route->post('webPageElement', [WebPageElementController::class, 'create'])->setName('webPageElement.create');

		// WEB SITE
		$route->post('webSite', [WebSiteController::class, 'create'])->setName('webSite.create');

		// DEFAULT
		$route->put('{type}', EntityUpdateController::class)->setName('entity.update');
		$route->delete('{type}',[ModuleActionsController::class, 'delete'])->setName('modules.delete');

	})->add(AuthMiddleware::class);

};