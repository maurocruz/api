<?php

declare(strict_types=1);

namespace Plinct\Api\Middleware;

use Plinct\Api\User\UserLogged;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoggedUserMiddleware implements MiddlewareInterface
{

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if ($request->hasHeader('Authorization')) {
			$authorizationBearer = $request->getHeaderLine('Authorization');
			if (preg_match("/Bearer\s+(.*)$/i", $authorizationBearer, $matches)) {
				$token = $matches[1];
				UserLogged::created($token);
		  }
		}
		return $handler->handle($request);
	}
}
