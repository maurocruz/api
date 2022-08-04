<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;

class UserActions implements HttpRequestInterface
{

	public function get(array $params = []): array
	{
		$dataUser = RequestApi::server()->getDataInBd('user');
		$dataUser->setParams($params);
		$data = $dataUser->render();

		// GET PERMISSIONS
		if (isset($params['properties']) && strpos($params['properties'],'permissions') !== false) {
			foreach ($data as $key => $valueData) {
				$iduser = $valueData['iduser'];
				$valueData['permissions'] = RequestApi::server()->user()->permissions()->get([`iduser`=>$iduser]);
				$data[$key] = $valueData;
			}
		}
		return $data;
	}

	public function post(array $params): array
	{
		$name = $params['name'] ?? null;
		$email = $params['email'] ?? null;
		$password = $params['password'] ?? null;
		$repeatPassword = $params['repeatPassword'] ?? null;

		if ($password !== $repeatPassword) {
			return ResponseApi::message()->fail()->passwordRepeatIsIncorrect();
		}
		if (strlen($name) < 5 ) {
			return ResponseApi::message()->fail()->nameLonger4Char();
		}
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			return ResponseApi::message()->fail()->invalidEmail();
		}
		if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$#", $password)) {
			return ResponseApi::message()->fail()->passwordLeastLength();
		}

		$newParams['name'] = $name;
		$newParams['email'] = $email;
		$newParams['password'] = password_hash($password, PASSWORD_DEFAULT);
		$data = RequestApi::server()->connectBd('user')->created($newParams);

		if (isset($data['error']) || (isset($data['status']) && $data['status'] == 'error')) {
			return ResponseApi::message()->error()->anErrorHasOcurred($data);
		} else {
			$id = RequestApi::server()->connectBd('user')->lastInsertId();
			return ResponseApi::message()->success()->success('User registered successfully', ['iduser' => $id] );
		}
	}

	public function put(array $params): array
	{
		return RequestApi::server()->connectBd('user')->update($params);
	}

	public function delete($params): array
	{
		return RequestApi::server()->connectBd('user')->delete($params);
	}

	/**
	 * @param array $params
	 * @return array
	 */
	protected function addPermission(array $params): array
	{
		if (UserLogged::isSuperUser()) {
			$iduser = $params['iduser'] ?? null;
			$function = $params['function'] ?? null;
			$namespace = $params['namespace'] ?? null;
			$actions = $params['actions'] ?? null;
			if ($iduser && $function && $namespace && $actions) {
				$returns = RequestApi::server()->connectBd('user_permissions')->created(['iduser'=>$iduser,'function'=>$function,'namespace'=>$namespace,'actions'=>$actions]);
				if (isset($returns['error'])) {
					return ResponseApi::message()->error()->anErrorHasOcurred($returns['error']);
				} else {
					return ResponseApi::message()->success()->success("Permissions added", $returns);
				}
			}
			return ResponseApi::message()->fail()->inputDataIsMissing();
		}
		return ResponseApi::message()->fail()->userNotAuthorizedForThisAction();
	}
}
