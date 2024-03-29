<?php

declare(strict_types=1);

namespace Plinct\Api\Request\User\Privileges;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Actions\Permissions;
use Plinct\Api\Request\Server\HttpRequest;
use Plinct\Api\Request\User\UserLogged;

class Privileges extends PrivilegesAbstract
{
	/**
	 * @return HttpRequest
	 */
	public function httpRequest(): HttpRequest
	{
		return new HttpRequest(new PrivilegesActions());
	}

	/**
	 * @param string $action
	 * @param string $namespace
	 * @param int|null $function
	 * @return void
	 */
	public function withPrivileges(string $action, string $namespace, int $function = null)
	{
		// IF SUPERUSER
		if (UserLogged::isSuperUser()) Permissions::setRequiresSubscription(true);
		// GET USERLOGGED PRIVILEGES
		$permissions = UserLogged::getPrivileges();
		if ($permissions) {
			foreach ($permissions as $value) {
				if (
					$value['function'] >= $function
					&& strpos($value['actions'], $action) !== false
					&& ($value['namespace'] === 'all' || $value['namespace'] == $namespace)
				)
					Permissions::setRequiresSubscription(true);
			}
		} else {
			Permissions::setRequiresSubscription(false);
		}
	}

	/**
	 * @param array $data
	 * @param string $method
	 * @return array
	 */
	public function filterGet(array $data, string $method = 'get'): array
	{
		// se for super usuário
		if(UserLogged::isSuperUser() || empty($data)) return $data;

		$newData = null;
		$permission = false;
		$itemList = isset($data['@type']) && $data['@type'] == 'ItemList';

		// se estiver em formato ItemList
		$itemListElement = $itemList ? $data['itemListElement'] : $data;

		foreach ($itemListElement as $key => $value) {
			$iduser_privileges = $value['iduser_privileges'] ?? null;
			$idUserCreator = $value['userCreator'] ?? null;
			// SE OS DADOS FOREM PRIVILEGIOS
			if ($iduser_privileges) {
				$permission = self::grantedPrivileges($value, $method);
			} // SE HOUVER NOS DADOS INFORMAÇÃO DO CRIADOR
			elseif ($idUserCreator) {
				// obtem permissões do criador
				$dataPermissions = (new PrivilegesActions())->get(['iduser' => $idUserCreator]);

				// se o privilegio for vazio
				if (empty($dataPermissions)) $permission = true;

				foreach ($dataPermissions as $valuePrivileges) {
					$permission = self::grantedPrivileges($valuePrivileges, $method);
					if ($permission === true) break;
				}

			} else {
				$permission = true;
			}
			if ($permission) $newData[$key] = $value;
		}

		if ($itemList) {
			$data['itemListElement'] = $newData;
			return $data;
		} elseif ($newData) {
			return $newData;
		} else {
			return ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__.' on line '.__LINE__);
		}
	}

	/**
	 * COMPARA OS PRIVILÉGIOS FUNÇÃO E NAMESPACE
	 * ENTRE O CURRET USER LOGGED E O CRIADOR DO DADO
	 * @param array $valuePrivileges
	 * @param string $method
	 * @return bool
	 */
	public function grantedPrivileges(array $valuePrivileges, string $method = 'get'): bool
	{
		// SE FOR SUPER USUARIO
		if (UserLogged::isSuperUser()) return true;
		// se for o mesmo usuario
		if ($valuePrivileges['iduser'] === ApiFactory::user()->userLogged()->getIduser()) return true;

		foreach (UserLogged::getPrivileges() as $userLoggedPrivileges) {
			$compareFunction = (
				$method == 'get'
					? $userLoggedPrivileges['function'] >= $valuePrivileges['function']
					: $userLoggedPrivileges['function'] > $valuePrivileges['function']
			);
			return (
				$compareFunction
				&& ($valuePrivileges['namespace'] === '' || $userLoggedPrivileges['namespace'] == $valuePrivileges['namespace'])
			);
		}
		return false;
	}

	/**
	 * @param string $needled
	 * @param string $haystacked
	 * @return bool
	 */
	public function permittedActions(string $needled, string $haystacked): bool
	{
		$returns = false;
		if (strpos($needled,'c') !== false) $returns = strpos($haystacked,'c') !== false;
		if (strpos($needled,'r') !== false) $returns = strpos($haystacked,'r') !== false;
		if (strpos($needled,'u') !== false) $returns = strpos($haystacked,'u') !== false;
		if (strpos($needled,'d') !== false) $returns = strpos($haystacked,'d') !== false;
		return $returns;
	}
}
