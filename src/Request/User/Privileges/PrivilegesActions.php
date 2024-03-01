<?php

declare(strict_types=1);

namespace Plinct\Api\Request\User\Privileges;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\HttpRequestInterface;
use Plinct\Api\Request\User\UserLogged;

class PrivilegesActions implements HttpRequestInterface
{
	const TABLENAME = 'user_privileges';


	public function getTable(): string
	{
		return self::TABLENAME;
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$data = new GetData(self::TABLENAME);
		$data->setParams($params);
		return $data->render();
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function post(array $params): array
	{
		$returns = false;
		// SE FOR SUPER USUARIO
		if (UserLogged::isSuperUser()) $returns = true;
		// SE NÃO COMPARA
		foreach (UserLogged::getPrivileges() as $valueLoggedPrivileges) {
			if (
				ApiFactory::user()->privileges()->permittedActions($params['actions'], $valueLoggedPrivileges['actions'])
				&& $params['function'] < $valueLoggedPrivileges['function']
				&& ($valueLoggedPrivileges['namespace'] === 'all' || $params['namespace'] === $valueLoggedPrivileges['namespace'])
			) {
				$returns = true;
			}
		}
		// RETURNS
		if ($returns) {
			$params['userCreator'] = ApiFactory::user()->userLogged()->getIduser();
			$data = ApiFactory::server()->connectBd(self::TABLENAME)->created($params);

			if (isset($data['error'])) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
			} else {
				return ApiFactory::response()->message()->success("privileges added");
			}
		}

		return ApiFactory::response()->message()->fail()->userNotAuthorizedForThisAction(__FILE__ . ' on ' . __LINE__);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array {
		return ApiFactory::server()->connectBd(self::TABLENAME)->update($params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array {
		return ApiFactory::server()->connectBd(self::TABLENAME)->delete($params);
	}
}
