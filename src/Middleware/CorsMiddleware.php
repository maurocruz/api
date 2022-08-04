<?php

declare(strict_types=1);

namespace Plinct\Api\Middleware;

use Plinct\Web\Debug\Debug;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
	private array $options = [];

	public function __construct(array $options)
	{
		$this->options = $options;
	}

	/**
	 * @inheritDoc
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$response = $handler->handle($request);
		foreach($this->options as $name => $value) {
			$response = $response->withHeader($name, $value);
		}
		//$response = $response->withHeader('Access-Control-Allow-Origin', '*');
		//$response = $response->withHeader('Access-Control-Allow-Headers', 'origin, x-requested-with, content-type, Authorization');
		//$response = $response->withHeader('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS');
		//$response = $response->withHeader("Content-type", "application/json");
		return $response;
	}
}