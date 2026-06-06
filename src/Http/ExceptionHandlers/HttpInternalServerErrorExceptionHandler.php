<?php
namespace Plinct\Api\Http\ExceptionHandlers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Throwable;

class HttpInternalServerErrorExceptionHandler
{
	public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response
	{
		var_dump($exception->getMessage());
		$response = new Response();
		$response->withStatus(500);
		$response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode(['status' => false, 'message' => 'Route not found']));
		return $response;
	}
}
