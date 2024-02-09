<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Plinct\Api\ApiFactory;
use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\User\Privileges\PrivilegesActions;

class UserActions implements HttpRequestInterface
{
	const tableName = "user";

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

	public function post(array $params): array
	{
		$name = $params['name'] ?? null;
		$email = $params['email'] ?? null;
		$password = $params['password'] ?? null;
		$passwordRepeat = $params['passwordRepeat'] ?? null;

		if ($password !== $passwordRepeat) {
			return ApiFactory::response()->message()->fail()->passwordRepeatIsIncorrect();
		}
		if (strlen($name) < 5) {
			return ApiFactory::response()->message()->fail()->nameLonger4Char();
		}
		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
			return ApiFactory::response()->message()->fail()->invalidEmail();
		}
		if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$#", $password)) {
			return ApiFactory::response()->message()->fail()->passwordLeastLength();
		}

		$newParams['name'] = $name;
		$newParams['email'] = $email;
		$newParams['password'] = password_hash($password, PASSWORD_DEFAULT);
		$data = ApiFactory::server()->connectBd('user')->created($newParams);

		if (isset($data['error']) || (isset($data['status']) && $data['status'] == 'error')) {
			return ApiFactory::response()->message()->error()->anErrorHasOcurred($data);
		} else {
			$id = ApiFactory::server()->connectBd('user')->lastInsertId();
			return ApiFactory::response()->message()->success()->success('User registered successfully', ['iduser' => $id]);
		}
	}

	public function put(array $params = null): array
	{
		return ApiFactory::server()->connectBd('user')->update($params);
	}

	public function delete($params): array
	{
		return ApiFactory::server()->connectBd('user')->delete($params);
	}

	public function getTable(): string
	{
		return self::tableName;
	}
}
