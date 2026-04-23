<?php

use Plinct\Api\Http\Controller\Entities\Action\ActionController;
use Plinct\Api\Http\Controller\Entities\EntityDeleteController;
use Plinct\Api\Http\Controller\Entities\EntityReadController;
use Plinct\Api\Http\Controller\Entities\EntityUpdateController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\Collection\CollectionController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\CreativeWorkController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\ImageObject\ImageObjectController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\MediaObjectCreateController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\MediaObjectReadController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPage\WebPageController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPageElement\WebPageElementController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebSite\WebSiteController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\PropertyValue\PropertyValueController;
use Plinct\Api\Http\Controller\Entities\Thing\Product\ProductController;
use Plinct\Api\Http\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	// ACTION
	$route->get('action', [ActionController::class,'read'])->setName('action.read');
	// COLLECTION
	$route->get('collection', [CollectionController::class, 'read'])->setName('collection.read');
	// CREATIVE WORK
	$route->get('creativeWork', [CreativeWorkController::class, 'read'])->setName('creativeWork.read');
	// IMAGE OBJECT
	$route->get('imageObject', [ImageObjectController::class, 'read'])->setName('imageObject.read');
	// MEDIA OBJECT
	$route->get('mediaObject', MediaObjectReadController::class)->setName('mediaObject.read');
	// PRODUCT
	$route->get('product', [ProductController::class,'read'])->setName('product.read');
	// PROPERTY VALUE
	$route->get('propertyValue', [PropertyValueController::class,'read'])->setName('propertyValue.read');
	// WEB PAGE
	$route->get('webPage', [WebPageController::class, 'read'])->setName('webPage.read');
	// WEB PAGE ELEMENT
	$route->get('webPageElement', [WebPageElementController::class, 'read'])->setName('webPageElement.read');
	// WEB SITE
	$route->get('webSite', [WebSiteController::class, 'read'])->setName('webSite.read');
	// DEFAULT
	$route->get('{type}',[EntityReadController::class, 'read'])->setName('modules.read');

	//
	// METHODS ACTIONS
	//
	$route->group('', function (RouteCollectorProxy $route)
	{
		// ACTION
		$route->post('action', [ActionController::class,'create'])->setName('modules.create');
		// COLLECTION
		$route->post('collection', [CollectionController::class, 'create'])->setName('collection.create');
		// IMAGE OBJECT
		$route->post('imageObject', [ImageObjectController::class, 'create'])->setName('imageObject.create');
		// MEDIA OBJECT
		$route->post('mediaObject', MediaObjectCreateController::class)->setName('mediaObject.create');
		// PRODUCT
		$route->post('product', [ProductController::class, 'create'])->setName('product.create');
		// PROPERTY VALUE
		$route->post('propertyValue', [PropertyValueController::class, 'create'])->setName('propertyValue.create');
		// WEB PAGE
		$route->post('webPage', [WebPageController::class, 'create'])->setName('webPage.create');
		// WEB PAGE ELEMENT
		$route->post('webPageElement', [WebPageElementController::class, 'create'])->setName('webPageElement.create');
		// WEB SITE
		$route->post('webSite', [WebSiteController::class, 'create'])->setName('webSite.create');
		// DEFAULT
		$route->put('{type}', EntityUpdateController::class)->setName('entity.update');
		$route->delete('{type}', EntityDeleteController::class)->setName('modules.delete');
	})->add(AuthMiddleware::class);
};
