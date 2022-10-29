<?php

declare(strict_types=1);

namespace Plinct\Api\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
	/**
	 * 'Access-Control-Allow-Origin': '*'
	 * 'Access-Control-Allow-Headers': 'origin, x-requested-with, content-type, Authorization'
	 * 'Access-Control-Allow-Methods': 'PUT, GET, POST, DELETE, OPTIONS'
	 * 'Content-type': 'application/json'
	 * @var array
	 */
	private array $options;

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
		return $response;
	}
}