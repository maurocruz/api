<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Plinct\Api\PlinctApi;
use Plinct\Api\User\Privileges\PrivilegesActions;

class UserLogged extends UserAbstract
{
	/**
	 * @param $token
	 * @return void
	 */
	public static function created($token)
	{
		if (is_string($token)) {
			try {
				$payload = JWT::decode($token, new Key(PlinctApi::$JWT_SECRET_API_KEY,'HS256'));
			} catch (ExpiredException $e) {
				$explodeToken = explode(".", $token);
				$payload = json_decode(base64_decode($explodeToken[1]));
			}
		} else {
			$payload = $token;
		}
		$idUserLogged = $payload->uid;
		parent::setIduser($idUserLogged);
		self::setExp((int)$payload->exp);

		$userData = (new UserActions())->get(['iduser'=>$idUserLogged]);

		if (!isset($userData['status']) || $userData['status'] !== 'fail') {
			$userValues = $userData[0];
			parent::setName($userValues['name']);
			parent::setEmail($userValues['email']);
			$dataPrivileges = (new PrivilegesActions())->get(['iduser'=>$idUserLogged]);
			parent::setPrivileges($dataPrivileges);
		}
	}

	public static function getProperties(): ?array
	{
		return self::getIduser() ? [
			"iduser" => self::getIduser(),
			"name" => self::getName(),
			"privileges" => self::getPrivileges()
		] : null;
	}

	/**
	 * @param array $userInvestigated
	 * @return array
	 */
	public static function comparePermissions(array $userInvestigated): array
	{
		$permissions = [];

		foreach($userInvestigated as $valueInvestigated) {
			// SE FOR DO MESMO USUARIO
			if (self::getIduser() == $valueInvestigated['iduser']) {
				$permissions[] = $valueInvestigated;
			} else {
				$permitted = false;
				foreach(self::getPrivileges() as $valueUserLogged) {
					// SE A FUNÇÃO FOR MAIOR OU IGUAL
					if ($valueInvestigated['function'] <= $valueUserLogged['function']) {
						$permitted = true;
					}
				}
				if($permitted) $permissions[] = $valueInvestigated;
			}
		}
		return $permissions;
	}
}
