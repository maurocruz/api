<?php

use Plinct\Api\Http\ExceptionHandlers\HttpBadRequestExceptionHandler;
use Plinct\Api\Http\ExceptionHandlers\HttpInternalServerErrorExceptionHandler;
use Plinct\Api\Http\ExceptionHandlers\HttpNotFoundExceptionHandler;
use Plinct\Api\Http\Middleware\GlobalMiddleware;
use Slim\App as SlimApp;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

return function (SlimApp $slimApp, bool $debug = false)
{
	// PHP ERRORS
	error_reporting($debug ? E_ALL : 0);

	// SLIM MIDDLEWARES
	$slimApp->addRoutingMiddleware();
	$slimApp->addBodyParsingMiddleware();
	$errorMiddleware = $slimApp->addErrorMiddleware($debug, $debug, $debug);

	// PLINCT MIDLEWARES
	$slimApp->add(GlobalMiddleware::class);

	// 500 INTERNAL SERVER ERROR
	$errorMiddleware->setErrorHandler(HttpInternalServerErrorException::class, HttpInternalServerErrorExceptionHandler::class);

	// 404 ROUTE NOT FOUND
	$errorMiddleware->setErrorHandler(HttpNotFoundException::class, HttpNotFoundExceptionHandler::class);

	// BAD REQUEST
	$errorMiddleware->setErrorHandler(HttpBadRequestException::class, HttpBadRequestExceptionHandler::class);
};
