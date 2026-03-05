<?php

use Plinct\Api\Http\ExceptionHandlers\HttpInternalServerErrorExceptionHandler;
use Plinct\Api\Http\ExceptionHandlers\HttpNotFoundExceptionHandler;
use Plinct\Api\Http\Middleware\GlobalMiddleware;
use Plinct\Api\Middleware\CorsMiddleware;
use Plinct\Api\Middleware\LoggedUserMiddleware;
use Slim\App as SlimApp;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

return function (SlimApp $slimApp, bool $debug = false) {
	// PHP ERRORS
	error_reporting($debug ? E_ALL : 0);
	// MIDDLEWARES
	$slimApp->addRoutingMiddleware();
	// ERROR HANDLER
	$errorMiddleware = $slimApp->addErrorMiddleware($debug, $debug, $debug);

	$slimApp->addMiddleware(new CorsMiddleware(["Content-type"=>"application/json", "Access-Control-Allow-Origin"=>"*"]));
	$slimApp->addMiddleware(new LoggedUserMiddleware());
	$slimApp->add(GlobalMiddleware::class);

	$errorMiddleware->setErrorHandler(HttpInternalServerErrorException::class, HttpInternalServerErrorExceptionHandler::class);
	// 404 NOT found
	$errorMiddleware->setErrorHandler(HttpNotFoundException::class, HttpNotFoundExceptionHandler::class);
};