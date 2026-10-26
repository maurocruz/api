<?php
namespace Plinct\Api\Application\User;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

readonly class UserDeleteUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) { }

	public function delete(array $queryParams): array
	{
		// SANITIZE PARAMS
		$queryParams = SanitizeParams::sanitize('user', $queryParams);
		$iduser = $queryParams['iduser'] ?? null;
		if (!$iduser) {
			return ['status' => false, 'message' => 'iduser is required', 'data'=>[]];
		}
		// BUSCA USUÁRIO
		$userData = $this->databaseActions->read('user', ['iduser' => $iduser])->render();
		if ($userData['status'] === false) {
			return $userData;
		}
		$user = $userData['data'][0] ?? null;
		if (!$user) {
			return ['status' => false, 'message' => 'User not found', 'data'=>[]];
		}
		// APAGA USUÁRIO
		$deleteData = $this->databaseActions->delete('user', ['iduser' => $iduser]);
		if ($deleteData['status'] === false) {
			return ['status' => false, 'message' => 'User not deleted', 'data'=>[$user]];
		}
		return ['status' => true, 'message' => 'User deleted', 'data'=>[$user]];
	}
}
