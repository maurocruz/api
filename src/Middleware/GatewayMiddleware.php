<?php
namespace Plinct\Api\Middleware;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class GatewayMiddleware implements MiddlewareInterface {
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
			if (PDOConnect::testConnection() === false) {
				$response = new Response();
				ApiFactory::response()->write($response, PDOConnect::getError());
				return $response;
			} else {
				return $handler->handle($request);
			}
    }
}
