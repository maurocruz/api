<?php
namespace Plinct\Api\Http\Controller;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticatorController
{
	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	public function isValidToken(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
	{
		$headers = getallheaders();
		$token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
		try {
			$decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
			$response->getBody()->write(json_encode(['status'=>'success',"message" => "Valid token", "data" => $decoded]));
		} catch (Exception $e) {
			$response = $response->withStatus(401);
			$response->getBody()->write(json_encode(['status'=>'fail',"message" => "Invalid token", 'data' => $e]));
		}
		return $response;
	}
}
