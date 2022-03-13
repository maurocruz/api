<?php

declare(strict_types=1);

use Plinct\Api\Auth\AuthController;
use Plinct\Api\Auth\Authentication;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
    /**
     * LOGIN
     */
    $route->map(['OPTIONS','POST'],'/login', function (Request $request, Response $response)
    {
        //$data = Auth\Authentication::login($request->getParsedBody()); // ERRO COM CORS
        $data = (new AuthController())->login($request->getParsedBody());

        $newResponse = $response->withHeader("Content-type", "'application/json'")
            ->withHeader('Access-Control-Allow-Origin','*')
            ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type');

        $newResponse->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return $newResponse;
    });

    $route->post('/reset_password', function (Request $request, Response $response)
    {
        $data = Authentication::resetPassword($request->getParsedBody());

        $response = $response->withHeader("Content-type", "'application/json'")
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type');

        $response->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response;
    });

    $route->post('/change_password', function (Request $request, Response $response)
    {
        $data = Authentication::changePassword($request->getParsedBody());

        $newResponse = $response->withHeader("Content-type", "'application/json'")
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type');

        $newResponse->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        return $newResponse;
    });
};
