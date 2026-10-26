<?php
namespace Plinct\Api\Application\User;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

readonly class UserUpdateUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) {	}

	public function update(array $params = []): array
	{
		$iduser = $params['iduser'] ?? null;
		if (!$iduser) {
			return ['status' => false, 'message' => 'Iduser is required'];
		}
		// SANITIZE PARAMS
		$params = SanitizeParams::sanitize('user', $params);
		unset($params['iduser']);
		// ALTERAR DADOS NO BANCO DE DADOS
		$updateData = $this->databaseActions->update('user', $params, ['iduser' => $iduser]);
		if ($updateData['status'] === false) {
			return $updateData;
		}
		return ['status' => true, 'message' => 'User updated', 'data' => [$params]];
	}
}
