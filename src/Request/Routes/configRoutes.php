<?php
declare(strict_types=1);

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
			$createTable = $params['createTable'] ?? null;
			$schema = $params['schema'] ?? null;
			if ($showTableStatus) {
				$data = ApiFactory::server()->configuration()->showTableStatus($showTableStatus);
			}
			if ($createTable) {
				$data = ApiFactory::server()->configuration()->createTable($createTable);
			}
			if ($schema === 'basic') {
				$data = ApiFactory::server()->configuration()->setBasicConfiguration();
			}
			return ApiFactory::response()->write($response, $data);
		});
	});
};
