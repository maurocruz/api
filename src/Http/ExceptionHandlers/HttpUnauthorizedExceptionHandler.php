<?php
namespace Plinct\Api\Http\ExceptionHandlers;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Throwable;

class HttpUnauthorizedExceptionHandler
{
	public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response
	{
		$response = new Response();
		$response = $response->withStatus(401);
		$response->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(json_encode(['status' => false, 'message' => 'Unauthorized access','data'=>$exception]));
		return $response;
	}
}
