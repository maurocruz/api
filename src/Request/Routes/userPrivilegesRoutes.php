<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

return function(Route $route)
{
	/**
	 * GET
	 */
	$route->get('', function (ServerRequestInterface $request, ResponseInterface $response) {
		$data = ApiFactory::server()->user()->privileges()->httpRequest()->withPrivileges('r','user_admin')->get($request->getQueryParams());
		return ApiFactory::response()->write($response,$data);
	});

	/**
	 * POST
	 *
	 * Adiciona privilégios ao usuário
	 * O usuário corrente, logado, pode adicionar privilegios a qualquer um,
	 * mas nunca nível acima dos seus prórpios privilágios
	 *
	 */
	$route->post('', function (ServerRequestInterface $request, ResponseInterface $response) {
		$data = ApiFactory::server()->user()->privileges()->httpRequest()->withPrivileges('c','user_admin')->post($request->getParsedBody());
		return ApiFactory::response()->write($response, $data);
	});

	/**
	 * PUT
	 */
	$route->put('', function(ServerRequestInterface $request, ResponseInterface $response) {
		$data = ApiFactory::server()->user()->privileges()->httpRequest()->withPrivileges('u','user_admin')->put($request->getParsedBody());
		return ApiFactory::response()->write($response, $data);
	});

	/**
	 * DELETE
	 */
	$route->delete('', function(ServerRequestInterface $request, ResponseInterface $response) {
		$data = ApiFactory::server()->user()->privileges()->httpRequest()->withPrivileges('d','user_admin',3)->delete($request->getQueryParams());
		return ApiFactory::response()->write($response, $data);
	});
};
