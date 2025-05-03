<?php
namespace Plinct\Api\Request\User;

use Plinct\Api\Request\Actions\Actions;
use Plinct\Api\Request\Server\Entity;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\HttpRequest;
use Plinct\Api\Request\User\Auth\Authentication;
use Plinct\Api\Request\User\Permission\Permissions;
use Plinct\Api\Request\User\Privileges\Privileges;

class User extends Entity
{
	/**
	 * @param array|null $params
	 * @return array
	 */
	public function get(array $params = null): array
	{
		$properties = self::propertiesToArray($params['properties'] ?? null);
		$params['fields'] = $params['fields'] ?? 'iduser,name,email,dateCreated,dateModified';
		$dataGet = New GetData('user');
		$dataGet->setParams($params);
		$data = $dataGet->render();
		if (!empty($data) && $properties) {
			foreach ($data as $key => $value) {
				$iduser = $value['iduser'];
				// PRIVILEGES
				if (in_array(('privileges'), $properties)) {
					$dataPrivileges = (new GetData('user_privileges'))->setParams(['iduser'=>$iduser])->render();
					if (isset($dataPrivileges[0])) {
						if (in_array(('userCreator'), $properties)) {
							foreach ($dataPrivileges as $keyPrivileges => $valuePrivileges) {
								$dataUserCreator = (new GetData('user'))->setParams(['iduser'=>$valuePrivileges['userCreator'],'fields'=>'iduser,name'])->render();
								$dataPrivileges[$keyPrivileges]['userCreator'] = $dataUserCreator[0];
							}
						}
						$data[$key]['privileges'] = $dataPrivileges;
					}
				}
			}
		}
		return $data;
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
