<?php
namespace Plinct\Api\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Plinct\Api\ApiApp;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;
use Throwable;

class AuthMiddleware implements MiddlewareInterface
{
  public function process(Request $request, Handler $handler): ResponseInterface
  {
	  $auth = $request->getHeaderLine('Authorization');

	  if (!preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
		  return $this->unauthorized('Token ausente');
	  }
	  try {
		  $decoded = JWT::decode(
			  $matches[1],
			  new Key(ApiApp::$JWT_SECRET_API_KEY, 'HS256')
		  );
	  } catch (Throwable) {
		  return $this->unauthorized('Token inválido');
	  }
	  // Injeta os dados do token no request
	  $request = $request->withAttribute('jwt', (array) $decoded);
	  return $handler->handle($request);
  }

	private function unauthorized(string $message): ResponseInterface
	{
		$response = new Response();
		$response->getBody()->write(json_encode([
			'error' => $message
		]));
		return $response
			->withStatus(401)
			->withHeader('Content-Type', 'application/json');
	}
}
