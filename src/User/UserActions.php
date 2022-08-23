<?php

declare(strict_types=1);

namespace Plinct\Api\User;

use Plinct\Api\Interfaces\HttpRequestInterface;
use Plinct\Api\Request\RequestApi;
use Plinct\Api\Response\ResponseApi;
use Plinct\Api\User\Privileges\Privileges;
use Plinct\Api\User\Privileges\PrivilegesActions;

class UserActions implements HttpRequestInterface
{
	public function get(array $params = []): array
	{
		$dataUser = RequestApi::server()->getDataInBd('user');
		$dataUser->setParams($params);
		$data = $dataUser->render();

		// GET PERMISSIONS
		if (isset($params['properties']) && strpos($params['properties'], 'privileges') !== false) {
			foreach ($data as $key => $valueData) {
				$iduser = $valueData['iduser'];
				$dataPrivileges = (new PrivilegesActions())->get(['iduser' => $iduser]);
				//
				foreach($dataPrivileges as $value) {
					if (Privileges::grantedPrivileges($value)) {
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
			return ResponseApi::message()->fail()->passwordRepeatIsIncorrect();
		}
		if (strlen($name) < 5) {
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
			return ResponseApi::message()->success()->success('User registered successfully', ['iduser' => $id]);
		}
	}

	public function put(array $params = null): array
	{
		return RequestApi::server()->connectBd('user')->update($params);
	}

	public function delete($params): array
	{
		return RequestApi::server()->connectBd('user')->delete($params);
	}
}
