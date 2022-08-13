<?php

declare(strict_types=1);

namespace Plinct\Api\User\Permission;

use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;
use Plinct\Api\Server\GetData\GetData;
use Plinct\Api\User\UserLogged;

class PermissionActions implements HttpRequestInterface
{
	const TABLENAME = 'user_privileges';

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function get(array $params = null): array
	{
		$data = new GetData(self::TABLENAME);
		$data->setParams($params);
		return $data->render();
	}

	/**
	 * @param array $params
	 * @return array
	 */
	public function post(array $params = []): array
	{
		if (isset($params['iduser'])) {
			// seta o criador da permissão
			$params['userCreator'] = UserLogged::getIduser();
			// salva no bd
			$returns = RequestApi::server()->connectBd(self::TABLENAME)->created($params);
			// returns
			if (isset($returns['error'])) {
				return ResponseApi::message()->error()->anErrorHasOcurred($returns['error']);
			} else {
				return ResponseApi::message()->success()->success("Permissions added", $returns);
			}
		}
		return ResponseApi::message()->fail()->inputDataIsMissing();
	}

	public function put(array $params = null): array
	{
		if (isset($params['iduser_permission']) && isset($params['iduser'])) {
			// salva no bd
			$returns = RequestApi::server()->connectBd(self::TABLENAME)->update($params);

			if (isset($returns['error'])) {
				return ResponseApi::message()->error()->anErrorHasOcurred($returns['error']);
			} else {
				return ResponseApi::message()->success()->success("Permission updated", $returns);
			}
		} else {
			return ResponseApi::message()->fail()->inputDataIsMissing();
		}
	}

	public function delete(array $params): array
	{
		if (isset($params['iduser_permission']) && isset($params['iduser'])) {
			$newParams = ['iduser_permission'=>$params['iduser_permission'], 'iduser'=>$params['iduser']];
			$returns = RequestApi::server()->connectBd(self::TABLENAME)->delete($newParams);
			if (isset($returns['error'])) {
				return ResponseApi::message()->error()->anErrorHasOcurred($returns['error']);
			} else {
				return ResponseApi::message()->success()->success("Permission deleted", $returns);
			}
		} else {
			return ResponseApi::message()->fail()->inputDataIsMissing();
		}
	}
}
