<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Plinct\Api\PlinctApi;
use Plinct\Api\Request\RequestApi;
use Plinct\Web\Debug\Debug;

class UserLogged extends UserAbstract
{
	public static function created($token)
	{
		if (is_string($token)) {
			try {
				$payload = JWT::decode($token, new Key(PlinctApi::$JWT_SECRET_API_KEY,'HS256'));
			} catch (ExpiredException $e) {
				self::setExpiredToken(true);
				list($header, $payload, $signature) = explode(".", $token);
				$header = json_decode(base64_decode($header));
				$payload = json_decode(base64_decode($payload));
				$signature = json_decode(base64_decode($signature));

			}
		} else {
			$payload = $token;
		}

		$idUserLogged = $payload->uid;
		parent::setIduser($idUserLogged);
		self::setExp((int)$payload->exp);

		$userData = RequestApi::server()->user()->httpRequest()->get(['iduser'=>$idUserLogged,'properties'=>'permissions']);
		$userValues = $userData[0];

		parent::setName($userValues['name']);
		parent::setEmail($userValues['email']);
		parent::setPermission($userValues['permissions']);
	}

	public function expired(): bool
	{
		return self::getExp() > (new \DateTime())->getTimestamp();
	}

	public function havePermission(int $functions = 1, string $actions = 'r', array $namespace = null): bool
	{
		if (self::isSuperUser()) {
			return true;
		}

		// functions
		Debug::dump(self::getPermission());

		return false;
	}
}
