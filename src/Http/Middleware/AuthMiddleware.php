<?php
namespace Plinct\Api\Http\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Plinct\Api\Application\User\UserLoggedUseCase;
use Plinct\Api\Domain\User\UserLogged;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Exception\HttpGoneException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Routing\RouteContext;
use Throwable;

readonly class AuthMiddleware implements MiddlewareInterface
{
	public function __construct(private UserLoggedUseCase $authLoginUseCase)
	{
	}

  public function process(Request $request, Handler $handler): ResponseInterface
  {
		$routeContext = RouteContext::fromRequest($request);
		$routeName = $routeContext->getRoute()->getName();

		// VERIFICAR PRESENÇA DO TOKEN
	  $auth = $request->getHeaderLine('Authorization');
	  if (!preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
		  throw new HttpUnauthorizedException($request);
	  }
		// DECODAR TOKEN
	  try {
		  $tokenDecoded = JWT::decode(
			  $matches[1],
			  new Key($_ENV['JWT_SECRET'], 'HS256')
		  );
	  } catch (Throwable) {
		  throw new HttpUnauthorizedException($request);
	  }

		// SALVAR USUÁRIO DO TOKEN
	  $userLogged = $this->authLoginUseCase->saveUserLogged($tokenDecoded);
		if ($userLogged['status'] === false) {
			throw new HttpGoneException($request);
		}
		$parts = explode('.', $routeName);
		$namespace = $parts[0];
		$action = $parts[1];
		if (!UserLogged::hasPermission($namespace, $action, 1)) {
			throw new HttpUnauthorizedException($request);
		}

	  // Injeta os dados do token no request
	  $request = $request->withAttribute('jwt', (array) $tokenDecoded);
	  return $handler->handle($request);
  }
}
