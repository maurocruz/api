<?php
declare(strict_types=1);
namespace Plinct\Api\Request\User;

use Plinct\Api\ApiFactory;
use Plinct\Api\Request\Server\HttpRequestInterface;
use Plinct\Api\Request\User\Privileges\PrivilegesActions;

class UserActions implements HttpRequestInterface
{
	/**
	 *
	 */
	const tableName = "user";

	/**
	 * @param array $params
	 * @return array
	 */
	public function get(array $params = []): array
	{
		$dataUser = ApiFactory::server()->getDataInBd(self::tableName);
		$dataUser->setParams($params);
		$data = $dataUser->render();

		// GET PERMISSIONS
		if (isset($params['properties']) && strpos($params['properties'], 'privileges') !== false) {
			foreach ($data as $key => $valueData) {
				$iduser = $valueData['iduser'];
				$dataPrivileges = (new PrivilegesActions())->get(['iduser' => $iduser]);
				//
				foreach($dataPrivileges as $value) {
					if (ApiFactory::user()->privileges()->grantedPrivileges($value)) {
						$valueData['privileges'][] = $value;
					}
				}

				$data[$key] = $valueData;
			}
		}
		return $data;
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function post(array $params = null): array
	{
		$name = $params['name'] ?? null;
		$email = $params['email'] ?? null;
		$password = $params['password'] ?? null;
		$passwordRepeat = $params['passwordRepeat'] ?? null;
		// check valid password
		if ($password !== $passwordRepeat) {
			return ApiFactory::response()->message()->fail()->passwordRepeatIsIncorrect();
		}
		// check valid name
		if (strlen($name) < 5) {
			return ApiFactory::response()->message()->fail()->nameLonger4Char();
		}
		// check valid email
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			return ApiFactory::response()->message()->fail()->invalidEmail();
		}
		//check valid password
		if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$#", $password)) {
			return ApiFactory::response()->message()->fail()->passwordLeastLength();
		}
		// save user
		$newParams['name'] = $name;
		$newParams['email'] = $email;
		$newParams['password'] = password_hash($password, PASSWORD_DEFAULT);

		$data = ApiFactory::request()->server()->connectBd('user')->created($newParams);
		// error
		if (isset($data['error']) || (isset($data['status']) && $data['status'] == 'error')) {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} else {
			// get iduser
			$iduser = ApiFactory::server()->connectBd('user')->lastInsertId();
			// insert user from new person
			// save thing
			$dataThing = ApiFactory::request()->type('thing')->httpRequest()->setPermission()->post(['name'=>$name,'type'=>'person']);
			$idthing = $dataThing['id'];
			// save person
			ApiFactory::request()->type('person')->httpRequest()->setPermission()->post(['thing'=>$idthing]);
			// save contactPoint
			ApiFactory::request()->type('contactPoint')->httpRequest()->setPermission()->post(['thing'=>$idthing,'email'=>$email]);
			// return
			return ApiFactory::response()->message()->success('User registered successfully', ['iduser' => $iduser]);
		}
	}

	/**
	 * @param array|null $params
	 * @return array
	 */
	public function put(array $params = null): array
	{
		return ApiFactory::server()->connectBd('user')->update($params);
	}

	/**
	 * @param $params
	 * @return array
	 */
	public function delete($params): array
	{
		return ApiFactory::server()->connectBd('user')->delete($params);
	}

	/**
	 * @return string
	 */
	public function getTable(): string
	{
		return self::tableName;
	}
}
