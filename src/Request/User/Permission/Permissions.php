<?php
namespace Plinct\Api\Request\User\Permission;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\HttpRequest;

class Permissions
{
	/**
	 * @return HttpRequest
	 */
	public function httpRequest(): HttpRequest
	{
		return new HttpRequest(new PermissionActions());
	}

	/**
	 * Verifica se o usuário logado, o que lê e requisita,
	 * tem função igual ou maior que o author dos dados.
	 * UserLogged.function ≥ userCreator.function
	 *
	 * @param $data
	 * @return array
	 */
	public static function permissionFilter($data): array
	{
		$newData = [];
		$boolean = false;

		foreach ($data as $value) {
			$iduser = ApiFactory::user()->userLogged()->getIduser();
			if ($iduser !== $value['userCreator']) {
				$boolean = true;
			}
			if ($boolean) $newData[] = $value;
		}
		return $newData;
	}
}

