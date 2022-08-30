<?php

declare(strict_types=1);

use Plinct\Api\Middleware\AuthMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\ApiFactory;

use Plinct\Api\Server\Format\Format;

return function(Route $route) {
	/**
	 * Generic GET
	 */
	$route->get('', function (Request $request, Response $response, $args)
	{
		$type = $args['type'] ?? null;
		$params = $request->getQueryParams() ?? null;
		$format = $params['format'] ?? null;

		if ($type) {
			$typeClass = ApiFactory::server()->type($type);

			if ($typeClass->exists()) {
				//  CLASS HIERARCHY
				if ($format == "classHierarchy") {
					$data = (Format::classHierarchy($type, $params))->ready();
				} elseif ($format == "geojson") {
					$data = Format::geojson($typeClass, $params)->ready();
				}
				//
				else {
					$data = $typeClass->httpRequest()->setPermission()->get($params);
				}
			} else {
				$data = ['status'=>'fail', 'message'=>'type not found!' ];
			}
		} else {
			$data = json_decode(file_get_contents(__DIR__.'/../composer.json'), true);
		}

		return ApiFactory::response()->write($response, $data);
	});

	/**
	 * PUT
	 */
	$route->put('', function (Request $request, Response $response, $args)
	{
		$type = $args['type'];
		$params = $request->getParsedBody() ?? null;
		$id = $params['id'] ?? $params['idIsPartOf'] ?? $params["id$type"] ?? null;

		if (!$id) {
			$data = ApiFactory::response()->message()->fail()->inputDataIsMissing($params);
		}
		elseif ($response->getStatusCode() === 200) {
			$typeClass = ApiFactory::server()->type($type);

			if($typeClass->exists()) {
				$data = ApiFactory::server()->type($type)->httpRequest()->withPrivileges('u', $type, 2)->put($params);
			} else {
				$data = ApiFactory::response()->message()->fail()->thisTypeNotExists();
			}
		}
		else {
			$data = ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction();
		}

		return ApiFactory::response()->write($response, $data);

	})->addMiddleware(new AuthMiddleware());

	/**
	 * POST
	 */
	$route->post('', function(Request $request, Response $response, $args)
	{
		$type = $args['type'] ?? null;
		$params = $request->getParsedBody();
		$action = $request->getParsedBody()['action'] ?? null;
		// NAMESPACE
		$namespace = $params['tableHasPart'] ?? $type;

		if($action == 'create') {
			$data = ApiFactory::server()->type($type)->create();
		} else {
			$data = ApiFactory::server()->type($type)->httpRequest()->withPrivileges('c', $namespace, 2)->post($params);
		}

		return ApiFactory::response()->write($response, $data);

	})->addMiddleware(new AuthMiddleware());
};
