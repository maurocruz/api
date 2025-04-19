<?php
namespace Plinct\Api\Request\User;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Plinct\Api\ApiApp;
use Plinct\Api\Request\User\Privileges\PrivilegesActions;

class UserLogged extends UserAbstract
{
	/**
	 * @param $token
	 * @return void
	 */
	public static function created($token): void
	{
		if (is_string($token)) {
			try {
				$payload = JWT::decode($token, new Key(ApiApp::$JWT_SECRET_API_KEY,'HS256'));
			} catch (ExpiredException) {
				$explodeToken = explode(".", $token);
				$payload = json_decode(base64_decode($explodeToken[1]));
			}
		} else {
			$payload = $token;
		}
		$idUserLogged = $payload->uid;
		parent::setIduser($idUserLogged);
		$userData = (new UserActions())->get(['iduser'=>$idUserLogged]);
		if (!empty($userData) && (!isset($userData['status']) || $userData['status'] !== 'fail')) {
			$userValues = $userData[0];
			parent::setName($userValues['name']);
			parent::setEmail($userValues['email']);
			$dataPrivileges = (new PrivilegesActions())->get(['iduser'=>$idUserLogged]);
			parent::setPrivileges($dataPrivileges);
		}
	}

	public static function getProperties(): ?array
	{
		return self::$iduser ? [
			"iduser" => self::$iduser,
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
			if (self::$iduser == $valueInvestigated['iduser']) {
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

	/**
	 * @param int|null $function
	 * @param string|null $actions
	 * @param string|null $namespace
	 * @return bool
	 */
	public function isPermitted(?int $function = null, string $actions = null, string $namespace = null): bool {
			foreach (self::getPrivileges() as $value) {
				$returns =  $function >= $value['function'] && str_contains($value['action'], $actions) && $namespace == $value['namespace'];
				if ($returns === true) return true;
			}
			return false;
	}
}
