<?php
declare(strict_types=1);
namespace Plinct\Api\Request\User;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Actions\Actions;
use Plinct\Api\Request\HttpRequest;
use Plinct\Api\Request\User\Auth\Authentication;
use Plinct\Api\Request\User\Permission\Permissions;
use Plinct\Api\Request\User\Privileges\Privileges;
use Plinct\PDO\PDOConnect;

class User
{
	/**
	 * @param array|null $params
	 * @return array
	 */
	public function get(array $params = null): array
	{
		$dataBd = ApiFactory::server()->getDataInBd('user');
		$dataBd->setParams($params);
		$data = $dataBd->render();
		$newData = [];
		foreach ($data as $item) {
			$privileges = ApiFactory::server()->getDataInBd('user_privileges')->setParams(['iduser'=>$item['iduser']])->render();
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

	public function actions(): Actions
	{
		return new Actions();
	}

	public function httpRequest(): HttpRequest
	{
		return new HttpRequest(new UserActions());
	}

	public function permissions(): Permissions
	{
		return new Permissions();
	}

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

  public function createSqlTable($type = null) : array
  {
	  $data = PDOConnect::run(file_get_contents(__DIR__ . '/User.sql'));
	  if (array_key_exists("error", $data)) {
		  return $data;
	  }
	  return [ "message" => "Sql table for ".$type. " created successfully!" ];
  }
}
