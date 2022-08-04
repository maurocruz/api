<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;
use Plinct\Api\User\User;

return function(Route $route)
{
	/**
	 * GET
	 */
	$route->get('', function (ServerRequestInterface $request, ResponseInterface $response)
	{
		if (User::userLogged()->havePermission(3)) {
			$dataUser = RequestApi::server()->user()->permissions()->get($request->getQueryParams());
			$data = ['status' => 'success','data'=>$dataUser];
		} else {
			$data = ResponseApi::message()::fail()->userNotAuthorizedForThisAction();
		}
		return ResponseApi::write($response,$data);
	});

	/**
	 * POST
	 */
	$route->post('', function (ServerRequestInterface $request, ResponseInterface $response)
	{
		if (User::userLogged()->havePermission(3)) {
			$parseBody = $request->getParsedBody();
			$data = RequestApi::server()->user()->permissions();
			\Plinct\Web\Debug\Debug::dump($data);
		}
		return ResponseApi::write($response);
	});
};
