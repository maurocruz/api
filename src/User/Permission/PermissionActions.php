<?php

declare(strict_types=1);

namespace Plinct\Api\User\Permission;

use Plinct\Api\ApiFactory;
use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\Server\GetData\GetData;

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
			$params['userCreator'] = ApiFactory::user()->userLogged()->getIduser();
			// salva no bd
			$returns = ApiFactory::server()->connectBd(self::TABLENAME)->created($params);
			// returns
			if (isset($returns['error'])) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($returns['error']);
			} else {
				return ApiFactory::response()->message()->success()->success("Permissions added", $returns);
			}
		}
		return ApiFactory::response()->message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
	}

	public function put(array $params = null): array
	{
		if (isset($params['iduser_permission']) && isset($params['iduser'])) {
			// salva no bd
			$returns = ApiFactory::server()->connectBd(self::TABLENAME)->update($params);

			if (isset($returns['error'])) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($returns['error']);
			} else {
				return ApiFactory::response()->message()->success()->success("Permission updated", $returns);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
		}
	}

	public function delete(array $params): array
	{
		if (isset($params['iduser_permission']) && isset($params['iduser'])) {
			$newParams = ['iduser_permission'=>$params['iduser_permission'], 'iduser'=>$params['iduser']];
			$returns = ApiFactory::server()->connectBd(self::TABLENAME)->delete($newParams);
			if (isset($returns['error'])) {
				return ApiFactory::response()->message()->error()->anErrorHasOcurred($returns['error']);
			} else {
				return ApiFactory::response()->message()->success()->success("Permission deleted", $returns);
			}
		} else {
			return ApiFactory::response()->message()->fail()->inputDataIsMissing(__FILE__.' on line '.__LINE__);
		}
	}

	public function getTable(): string
	{
		return self::TABLENAME;
	}
}
