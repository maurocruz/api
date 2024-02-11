<?php
declare(strict_types=1);
namespace Plinct\Api\Middleware;

use Plinct\Api\ApiFactory;

use Plinct\Api\Server\PDOConnect;
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
			$response = $handler->handle($request);
			if (PDOConnect::testConnection() === false) {
				$response = new Response();
				ApiFactory::response()->write($response, ['status'=>'fail', 'message'=>'Database not found!']);
			}
      return $response;
    }
}
