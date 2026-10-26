<?php
namespace Plinct\Api\Application\User;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;
use ReflectionException;

readonly class UserReadUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) { }

	/**
	 * @param array $params
	 * @return array
	 * @throws ReflectionException
	 */
	public function read(array $params = []): array
	{
		// SANITIZAR
		$params = SanitizeParams::sanitize('user', $params);
		$properties = $params['properties'] ?? [];
		// OBTÉM O(S) USUÁRIO(S)
		$getData = $this->databaseActions->read('user', $params);
		$dataUser = $getData->render();
		// IF FALSE
		if ($dataUser['status'] === false) {
			return $dataUser;
		}
		$userData = $dataUser['data'];
		// IF PROPERTIES
		if (!empty($properties)) {
			foreach ($userData as $key => $user) {
				$iduser = $user['iduser'];
				// PRIVILEGES
				if (in_array('privileges', $properties)) {
					$paramsPrivileges = in_array('userCreator',$properties) ? ['iduser' => $iduser, 'properties' => 'userCreator'] : ['iduser' => $iduser];
					$dataPrivileges = $this->userPrivileges($paramsPrivileges);
					if ($dataPrivileges['status'] === true) {
						$userData[$key]['privileges'] = $dataPrivileges['data'];
					}
				}
			}
		}
		return ['status' => true, 'message' => 'User found', 'data' => $userData];
	}

	/**
	 * @param array $params
	 * @return array
	 * @throws ReflectionException
	 */
	public function userPrivileges(array $params = []): array
	{
		$params = SanitizeParams::sanitize('user_privileges', $params);
		$properties = $params['properties'] ?? [];
		// BUSCAR DADOS NO BANDO DE DADOS
		$getData = $this->databaseActions->read('user_privileges', $params);
		$dataPrivileges = $getData->render();
		// IF FALSE
		if ($dataPrivileges['status'] === false) {
			return $dataPrivileges;
		}
		$userPrivileges = $dataPrivileges['data'];
		if (!empty($properties)) {
			foreach ($userPrivileges as $key => $user) {
				if (in_array('userCreator', $properties)) {
					$idUserCreator = $user['userCreator'];
					$dataUserCreator = $this->databaseActions->read('user', ['iduser' => $idUserCreator])->render();
					if ($dataUserCreator['status'] === true) {
						$userPrivileges[$key]['userCreator'] = $dataUserCreator['data'][0] ?? null;
					}
				}
			}
		}
		return ['status' => true, 'message' => 'User privileges found', 'data' => $userPrivileges];
	}
}
