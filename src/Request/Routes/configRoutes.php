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

	$route->group('/database', function (Route $route) {

		$route->get('', function (Request $request, Response $response) {
			$params = $request->getQueryParams();
			$data = ['message'=>'No action was taken'];
			$showTableStatus = $params['showTableStatus'] ?? null;
			$schema = $params['schema'] ?? null;
			if ($showTableStatus) {
				$data = ApiFactory::request()->configuration()->showTableStatus($showTableStatus);
			}
			if ($schema === 'basic') {
				$data = ApiFactory::request()->configuration()->setBasicConfiguration();
			}
			return ApiFactory::response()->write($response, $data);
		});

		$route->post('', function (Request $request, Response $response) {
			$params = $request->getParsedBody();
			$module = $params['createModule'] ?? null;
			$data = ApiFactory::request()->configuration()->createModule($module);
			return ApiFactory::response()->write($response, $data);
		})->addMiddleware(new AuthMiddleware());

	});
};
