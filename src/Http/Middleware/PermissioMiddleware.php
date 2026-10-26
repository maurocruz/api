<?php
namespace Plinct\Api\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteContext;

class PermissioMiddleware implements MiddlewareInterface
{

	/**
	 * @inheritDoc
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$routeContext = RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$nameRoute = $route->getName();
		$nameParts = is_string($nameRoute) ? explode(".",$nameRoute) : [];
		$namespace = reset($nameParts);
		$action = end($nameParts);


		return $handler->handle($request);
	}
}
