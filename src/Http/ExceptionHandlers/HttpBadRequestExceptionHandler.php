<?php
namespace Plinct\Api\Http\ExceptionHandlers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Throwable;

class HttpBadRequestExceptionHandler
{
	public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response
	{
		$response = new Response();
		$response = $response->withStatus(404)->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode(['status'=>false,"message" => $exception->getMessage()]));
		return $response;
	}
}
