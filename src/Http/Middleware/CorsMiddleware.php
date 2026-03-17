<?php
namespace Plinct\Api\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

readonly class CorsMiddleware implements MiddlewareInterface
{

	public function __construct(private bool $dev, private string $host)
	{	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$origin = $request->getHeaderLine('Origin');

		if ($this->dev === true) {
			$origins = ['*'];
		} else {
			$origins = [$this->host, 'https://'. $request->getUri()->getHost()];
		}

		$isAllowed = in_array($origin, $origins) || in_array('*', $origins);

		// resposta base
		$response = $request->getMethod() === 'OPTIONS'
			? new Response()
			: $handler->handle($request);

		if ($isAllowed) {
			return $response
				->withHeader('Access-Control-Allow-Origin', $origin === '' ? '*' : $origin)
				->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
				->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
				->withHeader('Access-Control-Allow-Credentials', 'true');
		}

		return $response;
	}
}
