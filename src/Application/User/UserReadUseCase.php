<?php
namespace Plinct\Api\Application\User;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

readonly class UserReadUseCase
{
	public function __construct(private DatabaseActions $databaseActions)
	{

	}

	public function read(array $params = []): array
	{
		// SANITIZAR
		$params = SanitizeParams::sanitize('user', $params);

		// OBTÉM O(S) USUÁRIO(S)
		$dataUser = $this->databaseActions->read('user',$params)->render();

		if ($dataUser['status'] === false) {
			return $dataUser;
		}
		$userData = $dataUser['data'];

		// IF PROPERTIES
		$properties = $params['properties'] ?? null;
		if ($properties === 'privileges') {
			foreach ($userData as $key => $user) {
				$iduser = $user['iduser'];
				$dataPrivileges = $this->databaseActions->read('user_privileges', ['iduser' => $iduser])->render();
				if ($dataPrivileges['status'] === true) {
					$userData[$key]['privileges'] = $dataPrivileges['data'];
				}
			}
		}

		return ['status' => true, 'message' => 'User found', 'data' => $userData];
	}

}
