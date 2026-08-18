<?php

use Plinct\Api\Http\Controller\Entities\Action\ActionController;
use Plinct\Api\Http\Controller\Entities\EntityController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\Book\BookController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\Collection\CollectionController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\CreativeWorkController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\ImageObject\ImageObjectController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\MediaObjectCreateController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\MediaObjectReadController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\MediaObject\VideoObject\VideoObjectController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\Review\ReviewController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPage\WebPageController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebPageElement\WebPageElementController;
use Plinct\Api\Http\Controller\Entities\Thing\CreativeWork\WebSite\WebSiteController;
use Plinct\Api\Http\Controller\Entities\Thing\Event\EventController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\Invoice\InvoiceController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\Offer\OfferController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\Order\OrderController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\ContactPoint\ContactPointController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\ContactPoint\PostalAddress\PostalAddressController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\GeoCoordinates\GeoCoordinatesController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\OrderItem\OrderItemController;
use Plinct\Api\Http\Controller\Entities\Thing\Intangible\StructuredValue\PropertyValue\PropertyValueController;
use Plinct\Api\Http\Controller\Entities\Thing\Organization\LocalBusinessController;
use Plinct\Api\Http\Controller\Entities\Thing\Organization\OrganizationController;
use Plinct\Api\Http\Controller\Entities\Thing\Place\PlaceController;
use Plinct\Api\Http\Controller\Entities\Thing\Product\ProductController;
use Plinct\Api\Http\Controller\Entities\Thing\Taxon\TaxonController;
use Plinct\Api\Http\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	// ACTION
	$route->get('action', [ActionController::class,'read'])->setName('action.read');
	// BOOK
	$route->get('book', [BookController::class, 'read'])->setName('book.read');
	// COLLECTION
	$route->get('collection', [CollectionController::class, 'read'])->setName('collection.read');
	// CONTACT POINT
	$route->get('contactPoint', [ContactPointController::class, 'read'])->setName('contactPoint.read');
	// CREATIVE WORK
	$route->get('creativeWork', [CreativeWorkController::class, 'read'])->setName('creativeWork.read');
	// EVENT
	$route->get('event', [EventController::class, 'read'])->setName('event.read');
	// GEOCOORDINATES
	$route->get('geoCoordinates', [GeoCoordinatesController::class, 'read'])->setName('geoCoordinates.read');
	// IMAGE OBJECT
	$route->get('{imageObject: ImageObject|imageObject}', [ImageObjectController::class, 'read'])->setName('imageObject.read');
	// LOCAL BUSINESS
	$route->get('localBusiness', [LocalBusinessController::class, 'read'])->setName('localBusiness.read');
	// MEDIA OBJECT
	$route->get('mediaObject', MediaObjectReadController::class)->setName('mediaObject.read');
	// OFFER
	$route->get('offer', [OfferController::class, 'read'])->setName('offer.read');
	// ORDER
	$route->get('order', [OrderController::class, 'read'])->setName('order.read');
	// ORDER ITEM
	$route->get('orderItem', [OrderItemController::class, 'read'])->setName('orderItem.read');
	// ORGANIZATION
	$route->get('organization', [OrganizationController::class, 'read'])->setName('organization.read');
	// PLACE
	$route->get('place', [PlaceController::class, 'read'])->setName('place.read');
	// PRODUCT
	$route->get('product', [ProductController::class,'read'])->setName('product.read');
	// PROPERTY VALUE
	$route->get('propertyValue', [PropertyValueController::class,'read'])->setName('propertyValue.read');
	// TAXON
	$route->get('taxon', [TaxonController::class, 'read'])->setName('taxon.read');
	// VIDEO OBJECT
	$route->get('{videoObject: videoObject|VideoObject}', [VideoObjectController::class, 'read'])->setName('videoObject.read');
	// WEB PAGE
	$route->get('webPage', [WebPageController::class, 'read'])->setName('webPage.read');
	// WEB PAGE ELEMENT
	$route->get('webPageElement', [WebPageElementController::class, 'read'])->setName('webPageElement.read');
	// WEB SITE
	$route->get('webSite', [WebSiteController::class, 'read'])->setName('webSite.read');
	// DEFAULT
	$route->get('{type}',[EntityController::class, 'read'])->setName('modules.read');

	//
	// METHODS ACTIONS
	//
	$route->group('', function (RouteCollectorProxy $route)
	{
		// ACTION
		$route->post('action', [ActionController::class,'create'])->setName('modules.create');
		// COLLECTION
		$route->post('collection', [CollectionController::class, 'create'])->setName('collection.create');
		// CONTACT POINT
		$route->post('contactPoint', [ContactPointController::class, 'create'])->setName('contactPoint.create');
		// EVENT
		$route->post('event', [EventController::class, 'create'])->setName('event.create');
		// GEO COORDINATES
		$route->post('geoCoordinates', [GeoCoordinatesController::class, 'create'])->setName('geoCoordinates.create');
		$route->put('geoCoordinates', [GeoCoordinatesController::class, 'update'])->setName('geoCoordinates.update');
		// IMAGE OBJECT
		$route->post('imageObject', [ImageObjectController::class, 'create'])->setName('imageObject.create');
		// INVOICE
		$route->post('invoice', [InvoiceController::class, 'create'])->setName('invoice.create');
		$route->put('invoice', [InvoiceController::class, 'update'])->setName('invoice.update');
		$route->delete('invoice', [InvoiceController::class, 'delete'])->setName('invoice.delete');
		// MEDIA OBJECT
		$route->post('mediaObject', MediaObjectCreateController::class)->setName('mediaObject.create');
		// ORDER
		$route->post('order', [OrderController::class, 'create'])->setName('order.create');
		$route->put('order', [OrderController::class, 'update'])->setName('order.update');
		$route->delete('order', [OrderController::class, 'delete'])->setName('order.delete');
		// ORDER iTEM
		$route->post('orderItem', [OrderItemController::class, 'create'])->setName('orderItem.create');
		$route->put('orderItem', [OrderItemController::class, 'update'])->setName('orderItem.update');
		$route->delete('orderItem', [OrderItemController::class, 'delete'])->setName('orderItem.delete');
		// POSTAL ADDRESS
		$route->post('postalAddress', [PostalAddressController::class, 'create'])->setName('place.create');
		$route->put('postalAddress', [PostalAddressController::class, 'update'])->setName('place.update');
		// PRODUCT
		$route->post('product', [ProductController::class, 'create'])->setName('product.create');
		// PROPERTY VALUE
		$route->post('propertyValue', [PropertyValueController::class, 'create'])->setName('propertyValue.create');
		// REVIEW
		$route->post('review',[ReviewController::class, 'create'])->setName('review.create');
		// WEB PAGE
		$route->post('webPage', [WebPageController::class, 'create'])->setName('webPage.create');
		// WEB PAGE ELEMENT
		$route->post('webPageElement', [WebPageElementController::class, 'create'])->setName('webPageElement.create');
		// WEB SITE
		$route->post('webSite', [WebSiteController::class, 'create'])->setName('webSite.create');
		// DEFAULT
		$route->post('{type}', [EntityController::class, 'create'])->setName('modules.create');
		$route->put('{type}', [EntityController::class, 'update'])->setName('entity.update');
		$route->delete('{type}', [EntityController::class, 'delete'])->setName('modules.delete');
	})->add(AuthMiddleware::class);
};
