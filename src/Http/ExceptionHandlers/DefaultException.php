<?php
namespace Plinct\Api\Http\ExceptionHandlers;

use Plinct\Api\Application\Support\SupportApplication;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Throwable;

class DefaultException
{

	public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): Response
	{
		$status = $exception->getCode() > 0 ? $exception->getCode() : 500;
		$message = $exception->getMessage();
		$file = $exception->getFile();
		$line = $exception->getLine();
		$response = new Response();
		$response = $response->withStatus($status)->withHeader('Content-Type', 'application/json');
		$response->getBody()->write(SupportApplication::returnDTOToJson($status, $message, $exception->getTrace(), $file.' on line: '.$line));
		return $response;
	}
}
