<?php
namespace Plinct\Api\Request\User;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Actions\Actions;
use Plinct\Api\Request\Server\HttpRequest;
use Plinct\Api\Request\User\Auth\Authentication;
use Plinct\Api\Request\User\Permission\Permissions;
use Plinct\Api\Request\User\Privileges\Privileges;

class User
{
	/**
	 * @param array|null $params
	 * @return array
	 */
	public function get(array $params = null): array
	{
		$dataBd = ApiFactory::request()->server()->getDataInBd('user');
		$dataBd->setParams($params);
		$data = $dataBd->render();
		$newData = [];
		foreach ($data as $item) {
			$privileges = ApiFactory::request()->server()->getDataInBd('user_privileges')->setParams(['iduser'=>$item['iduser']])->render();
			if (UserLogged::isSuperUser()) {
				$item['privileges'] = $privileges;
				$newData[] = $item;
			} elseif (empty($privileges) || $item['iduser'] === UserLogged::iduser()) {
				$item['privileges'] = $privileges;
				$newData[] = $item;
			} else {
				foreach ($privileges as $privilegeItem) {
					foreach (UserLogged::getPrivileges() as $privilegeUserLogged) {
						if ($privilegeUserLogged['function'] >= $privilegeItem['function']) {
							$item['privileges'] = $privilegeItem;
							$newData[] = $item;
						}
					}
				}
			}
		}
		return $newData;
	}

	/**
	 * @return Authentication
	 */
	public function authentication(): Authentication
	{
		return new Authentication();
	}

	/**
	 * @return Actions
	 */
	public function actions(): Actions
	{
		return new Actions();
	}

	/**
	 * @return HttpRequest
	 */
	public function httpRequest(): HttpRequest
	{
		return new HttpRequest(new UserActions());
	}

	/**
	 * @return Permissions
	 */
	public function permissions(): Permissions
	{
		return new Permissions();
	}

	/**
	 * @return Privileges
	 */
	public function privileges(): Privileges
	{
		return new Privileges();
	}

	/**
	 * @return UserLogged
	 */
	public function userLogged(): UserLogged {
		return new UserLogged();
	}
}
