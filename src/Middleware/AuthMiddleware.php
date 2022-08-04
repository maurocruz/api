<?php

declare(strict_types=1);

namespace Plinct\Api\Middleware;

use Plinct\Api\PlinctApi;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Tuupola\Middleware\JwtAuthentication;

class AuthMiddleware implements MiddlewareInterface
{
  public function process(Request $request, Handler $handler): ResponseInterface
  {
    /*$token = $request->getParsedBody()['token'] ?? $request->getQueryParams()['token'] ?? null;

		if($token) {
			$request = $request->withHeader("Authorization", "Bearer $token");
		}*/

		return (new JwtAuthentication(['secret'=>PlinctApi::$JWT_SECRET_API_KEY]))->process($request, $handler);
  }
}
