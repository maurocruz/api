<?php

declare(strict_types=1);

namespace Plinct\Api\Middleware;

use Plinct\Api\PlinctApp;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Tuupola\Middleware\JwtAuthentication;

class AuthMiddleware implements MiddlewareInterface
{
  public function process(Request $request, Handler $handler): ResponseInterface
  {
		return (new JwtAuthentication([
			'secure'=>true,
			'relaxed'=>['localhost','192.168.1.14'],
			'secret'=>PlinctApp::$JWT_SECRET_API_KEY]
		))->process($request, $handler);
  }
}
