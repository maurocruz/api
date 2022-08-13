<?php

declare(strict_types=1);

namespace Plinct\Api\User\Auth;

use Exception;
use Firebase\JWT\JWT;
use Plinct\Api\PlinctApi;
use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;
use Plinct\Api\User\UserActions;

class Authentication
{
	/**
	 * @param $params
	 * @return array
	 */
	public function login($params): array
	{
		// NO DATA RECEIVED
		if (!isset($params['email']) || !isset($params['password'])) {
			return ResponseApi::message()->fail()->inputDataIsMissing();
		}
		// INVALID EMAIL
		if (!Validator::isEmail($params['email'])) {
			return ResponseApi::message()->fail()->invalidEmail();
		}

		$email = filter_var($params['email'], FILTER_VALIDATE_EMAIL);
		$password = $params['password'] ?? null;
		$iss = $params['iss'] ?? PlinctApi::$ISSUER;
		$exp = $params['exp'] ?? PlinctApi::$JWT_EXPIRE;


		// GET DATA
		$data = (new UserActions())->get(["email" => $email ]);

		// ERROR
		if(isset($data['error'])) {
			return ResponseApi::message()->error()->anErrorHasOcurred($data['error']);
		}

		// USER NOT EXISTS
		if (empty($data)) {
			return ResponseApi::message()->fail()->userDoesNotExist();
		}

		// USER EXISTS
		if (password_verify($password, $data[0]['password'])) {
			$value = $data[0];
			$payload = [
				"iss" => $iss,
				"exp" => time() + $exp,
				"name" => $value['name'],
				"uid" => $value['iduser']
			];

			return ResponseApi::message()->success()->success("Access authorized",['token'=>JWT::encode($payload, PlinctApi::$JWT_SECRET_API_KEY)]);
		}

		// USER NOT AUTHORIZED
		return ResponseApi::message()->fail()->userExistsButNotLogged();
	}

	/**
	 * @throws Exception
	 */
	public function resetPassword(array $parseBody): array
  {
    return (new ResetPassword)->resetPassword($parseBody);
  }

  /**
   * @param array $bodyParams
   * @return array
   */
  public  function changePassword(array $bodyParams): array
  {
    return ResetPassword::changePassword($bodyParams);
  }
}
