<?php
use Plinct\Api\Middleware\AuthMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

return function(Route $route) {

	$route->get('', function (Request $request, Response $response) {
		return ApiFactory::response()->write($response, ['config'=>['modules enabled'=> ['Install modules','database','update']]]);
	});


	// INSTALL MODULES
	$route->post('/install', function (Request $request, Response $response) {
		$params = $request->getParsedBody();
		$module = $params['module'] ?? null;
		$data = ApiFactory::request()->configuration()->module()->installModule($module);
		return ApiFactory::response()->write($response, $data);
	})->addMiddleware(new AuthMiddleware());

	// DATABASE
	$route->group('/database', function (Route $route) {
		$route->get('', function (Request $request, Response $response) {
			$params = $request->getQueryParams();
			$data = ['message'=>'No action was taken'];
			$tableName = $params['showTableStatus'] ?? null;
			$action = $params['action'] ?? null;
			if ($tableName) {
				$data = ApiFactory::request()->server()->connectBd($tableName)->showTableStatus();
			}
			// ACTION
			if ($action === 'install') {
				$data = ApiFactory::request()->configuration()->module()->install($params);
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
