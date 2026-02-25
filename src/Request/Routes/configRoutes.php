<?php
use Plinct\Api\Middleware\AuthMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

return function(Route $route) {

	$route->get('', function (Request $request, Response $response) {
		$data =  ApiFactory::request()->configuration()->index();
		return ApiFactory::response()->write($response, ApiFactory::response()->configuration($data)->index());
	});

	// INIT
	$route->post('/installDatabase', function (Request $request, Response $response) {
		$params = $request->getParsedBody();
		$data = ApiFactory::request()->configuration()->module()->installDatabase($params);
		return ApiFactory::response()->write($response, $data);
	});

	// INSTALL MODULES
	$route->post('/installModule', function (Request $request, Response $response) {
		$params = $request->getParsedBody();
		$module = $params['module'] ?? null;
		if ($module) {
			$data = ApiFactory::request()->configuration()->module()->installModule($module);
		} else {
			$data = ['status'=>'fail','message'=>'Module was not created! Name is null!'];
		}
		return ApiFactory::response()->write($response, $data);
	})->addMiddleware(new AuthMiddleware());

	// DATABASE
	$route->group('/database', function (Route $route) {
		$route->get('', function (Request $request, Response $response) {
			$params = $request->getQueryParams();
			$data = ['message'=>'No action was taken'];
			$tableName = $params['showTableStatus'] ?? null;
			if ($tableName) {
				$data = ApiFactory::request()->server()->connectBd($tableName)->showTableStatus();
			}
			return ApiFactory::response()->write($response, $data);
		});
	});

	// UPDATE
	$route->group('/update', function (Route $route) {
		$route->get('', function (Request $request, Response $response) {
			$params = $request->getQueryParams();
			$data = [];
			if ($params['update'] == 'v2tov3') {
				$data = ApiFactory::request()->configuration()->update()->v2tov3();
			}
			return ApiFactory::response()->write($response, $data);
		});
	});
};
