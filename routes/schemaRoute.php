<?php
use Plinct\Api\Request\Schema\Schema;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

return function (RouteCollectorProxy $route)
{
	$route->group('schema', function (RouteCollectorProxy $route)
	{
		/**
		 * Generic GET
		 */
		$route->get('[/{class}]', function (Request $request, Response $response)
		{
			$params = $request->getQueryParams();

			$class = $request->getAttribute('class') ?? null;
			$schemas = $params['schemas'] ?? null;
			$subClass = $params['subClass'] ?? null;
			$subClassOf = $params['subClassOf'] ?? null;

			$schema = new Schema();
			$schema->setClass($class);
			$schema->setSchemas($schemas);
			$schema->setSubClass($subClass);
			$schema->setSubClassOf($subClassOf);
			$data = $schema->ready();


			$response = $response->withHeader('Content-Type', 'application/json');
			$response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ));
			return $response;
		});
	});
};