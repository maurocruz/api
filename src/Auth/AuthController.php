<?php

declare(strict_types=1);

namespace Plinct\Api\Auth;

use Firebase\JWT\JWT;
use Plinct\Api\PlinctApi;
use Plinct\Api\Type\User;
use Plinct\Tool\ArrayTool;

class AuthController
{
  /**
   * @param $params
   * @return array
   */
  public function login($params): array
  {
    // NO DATA RECEIVED
    if (!isset($params['email']) || !isset($params['password'])) {
      return [
        "status" => "fail",
        "message" => "Access rejected - Incomplete data received"
      ];
    }

    $email = filter_var($params['email'], FILTER_VALIDATE_EMAIL);
    $password = $params['password'] ?? null;
    $iss = $params['iss'] ?? PlinctApi::$ISSUER;
    $exp = $params['exp'] ?? PlinctApi::$JWT_EXPIRE;

    // INVALID EMAIL
    if ($email === false) {
      return [
        "status" => "fail",
        "message" => "Invalid email - Access unauthorized"
      ];
    }

    // GET DATA
    $data = (new User())->get([ "properties" => "*", "email" => $email ]);

    // ERROR
    if(isset($data['error'])) {
      return [
        "status" => "error",
        "message" => $data['error']['message']
      ];
    }

    // USER NOT EXISTS
    if (empty($data)) {
      return [
        "status" => "fail",
        "message" => "User not exists - Access unauthorized"
      ];
    }

    // USER EXISTS
    if (password_verify($password, $data[0]['password'])) {
      $value = $data[0];
      $payload = [
        "iss" => $iss,
        "exp" => time() + $exp,
        "name" => $value['name'],
        "admin" => $value['status'] == 1,
        "uid" => ArrayTool::searchByValue($value['identifier'], "id")['value']
      ];

      return [
        "status" => "success",
        "message" => "Access authorized",
        "token" => JWT::encode($payload, PlinctApi::$JWT_SECRET_API_KEY)
      ];
    }

    // USER NOT AUTHORIZED
    return [
      "status" => "fail",
      "message" => "The user exists but has not logged in. Check your password!"
    ];
  }

	/**
	 * @param array $params
	 * @return array
	 */
  public function register(array $params): array
  {
		if ($params['password'] !== $params['repeatPassword']) {
	    return [ "status" => 'fail', "message" => "Password repeat is incorrect" ];
    }
		unset($params['repeatPassword']);
		return (new User())->post($params);
  }
}
