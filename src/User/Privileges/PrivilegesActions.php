<?php

declare(strict_types=1);

namespace Plinct\Api\User\Privileges;

use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;
use Plinct\Api\Server\GetData\GetData;
use Plinct\Api\User\UserLogged;

class PrivilegesActions implements HttpRequestInterface
{
	const TABLENAME = 'user_privileges';

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
				Privileges::permittedActions($params['actions'], $valueLoggedPrivileges['actions'])
				&& $params['function'] < $valueLoggedPrivileges['function']
				&& $params['namespace'] === $valueLoggedPrivileges['namespace']
			) {
				$returns = true;
			}
		}
		// RETURNS
		if ($returns) {
			$params['userCreator'] = UserLogged::getIduser();
			$data = RequestApi::server()->connectBd(self::TABLENAME)->created($params);

			if (isset($data['error'])) {
				return ResponseApi::message()->error()->anErrorHasOcurred($data);
			} else {
				return ResponseApi::message()->success()->success("privileges added");
			}
		}

		return ResponseApi::message()->fail()->userNotAuthorizedForThisAction(__FILE__ . ' on ' . __LINE__);
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array {
		return RequestApi::server()->connectBd(self::TABLENAME)->update($params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function delete(array $params): array {
		return RequestApi::server()->connectBd(self::TABLENAME)->delete($params);
	}
}
