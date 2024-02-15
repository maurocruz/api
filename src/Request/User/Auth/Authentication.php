<?php
declare(strict_types=1);
namespace Plinct\Api\Request\User\Auth;

use Exception;
use Firebase\JWT\JWT;
use Plinct\Api\ApiFactory;
use Plinct\Api\PlinctApp;
use Plinct\Api\Request\User\UserActions;
use Plinct\Tool\ToolBox;

class Authentication
{
	/**
	 * @param $params
	 * @return array
	 */
	public function login($params): array
	{
		$password = $params['password'] ?? null;
		$logger = ToolBox::Logger('auth', PlinctApp::getLogdir().'auth.log');
		// NO DATA RECEIVED
		if (!isset($params['email']) || !isset($password)) {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing();
		}
		// INVALID EMAIL
		if (!Validator::isEmail($params['email'])) {
			return ApiFactory::response()->message()->fail()->invalidEmail();
		}
		$email = filter_var($params['email'], FILTER_VALIDATE_EMAIL);
		$iss = $params['iss'] ?? PlinctApp::$ISSUER;
		$exp = $params['exp'] ?? PlinctApp::$JWT_EXPIRE;
		// GET DATA
		$data = (new UserActions())->get(["email" => $email ]);
		// ERROR
		if(isset($data['error'])) {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data['error']);
		}
		// USER NOT EXISTS
		if (empty($data)) {
			$logger->info('ACCESS LOGIN: User does not exists!', ['email'=>$email]);
			return ApiFactory::response()->message()->fail()->userDoesNotExist();
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
			return ApiFactory::response()->message()->success()->success("Access authorized",['token'=>JWT::encode($payload, PlinctApp::$JWT_SECRET_API_KEY)]);
		}
		// USER NOT AUTHORIZED
		return ApiFactory::response()->message()->fail()->userExistsButNotLogged();
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function register($params): array
	{
		return (new UserActions())->post($params);
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
