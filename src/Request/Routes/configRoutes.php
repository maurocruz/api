<?php
declare(strict_types=1);

use Plinct\Api\Middleware\AuthMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

return function(Route $route) {

	$route->get('', function (Request $request, Response $response) {
		return ApiFactory::response()->write($response, ['config'=>'config']);
	});

	$route->post('/install', function (Request $request, Response $response) {
		$params = $request->getParsedBody();
		$module = $params['module'] ?? null;
		$data = ApiFactory::request()->configuration()->module()->installModule($module);
		return ApiFactory::response()->write($response, $data);
	})->addMiddleware(new AuthMiddleware());

	$route->group('/database', function (Route $route) {

		$route->get('', function (Request $request, Response $response) {
			$params = $request->getQueryParams();
			$data = ['message'=>'No action was taken'];
			$tableName = $params['showTableStatus'] ?? null;
			$schema = $params['schema'] ?? null;
			if ($tableName) {
				$data = ApiFactory::request()->server()->connectBd($tableName)->showTableStatus();
			}
			if ($schema === 'init') {
				$data = ApiFactory::request()->configuration()->module()->initApplication();
			}
			return ApiFactory::response()->write($response, $data);
		});


	});
};
