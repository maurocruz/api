<?php

use Plinct\Api\Http\ExceptionHandlers\DefaultException;
use Plinct\Api\Http\ExceptionHandlers\HttpBadRequestExceptionHandler;
use Plinct\Api\Http\ExceptionHandlers\HttpInternalServerErrorExceptionHandler;
use Plinct\Api\Http\ExceptionHandlers\HttpNotFoundExceptionHandler;
use Plinct\Api\Http\ExceptionHandlers\HttpUnauthorizedExceptionHandler;
use Plinct\Api\Http\Middleware\CorsMiddleware;
use Plinct\Api\Http\Middleware\GlobalMiddleware;
use Slim\App as SlimApp;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpUnauthorizedException;

return function (SlimApp $slimApp, array $settings = [])
{
	$dev = $settings['development'] ?? false;
	$host = $settings['host'] ?? 'localhost';

	// PHP ERRORS
	error_reporting($dev ? E_ALL : 0);

	// CORS MIDDLEWARE
	$slimApp->addMiddleware(new CorsMiddleware($dev, $host));

	// SLIM MIDDLEWARES
	$slimApp->addRoutingMiddleware();
	$errorMiddleware = $slimApp->addErrorMiddleware($dev, $dev, $dev);

	$slimApp->addBodyParsingMiddleware();

	// PLINCT MIDLEWARES
	$slimApp->add(GlobalMiddleware::class);

	// 401 UNAUTHORIZED ACCESS
	$errorMiddleware->setErrorHandler(HttpUnauthorizedException::class, HttpUnauthorizedExceptionHandler::class);

	// 500 INTERNAL SERVER ERROR
	$errorMiddleware->setErrorHandler(HttpInternalServerErrorException::class, HttpInternalServerErrorExceptionHandler::class);

	// 404 ROUTE NOT FOUND
	$errorMiddleware->setErrorHandler(HttpNotFoundException::class, HttpNotFoundExceptionHandler::class);

	// BAD REQUEST
	$errorMiddleware->setErrorHandler(HttpBadRequestException::class, HttpBadRequestExceptionHandler::class);

	// ERROR DEFAULT
	$errorMiddleware->setDefaultErrorHandler(DefaultException::class);
};
