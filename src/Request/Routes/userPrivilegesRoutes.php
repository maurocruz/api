<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;

return function(Route $route)
{
	/**
	 * GET
	 */
	$route->get('', function (ServerRequestInterface $request, ResponseInterface $response) {
		$data = RequestApi::server()->user()->privileges()->httpRequest()->withPrivileges('r','user_admin')->get($request->getQueryParams());
		return ResponseApi::write($response,$data);
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
		$data = RequestApi::server()->user()->privileges()->httpRequest()->withPrivileges('c','user_admin')->post($request->getParsedBody());
		return ResponseApi::write($response, $data);
	});

	/**
	 * PUT
	 */
	$route->put('', function(ServerRequestInterface $request, ResponseInterface $response) {
		$data = RequestApi::server()->user()->privileges()->httpRequest()->withPrivileges('u','user_admin')->put($request->getParsedBody());
		return ResponseApi::write($response, $data);
	});

	/**
	 * DELETE
	 */
	$route->delete('', function(ServerRequestInterface $request, ResponseInterface $response) {
		$data = RequestApi::server()->user()->privileges()->httpRequest()->withPrivileges('d','user_admin',3)->delete($request->getQueryParams());
		return ResponseApi::write($response, $data);
	});
};
