<?php
namespace Plinct\Api\Application\User;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Domain\User\UserLogged;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

readonly class UserPrivilegesUseCase
{
	public function __construct(private DatabaseActions $databaseActions)
	{
	}

	// READ
	public function read(array $queryParam): array
	{
		// SANITIZE PARAMS
		$queryParam = SanitizeParams::sanitize('user_privileges', $queryParam);
		// BUSCAR PRIVILEGES
		return $this->databaseActions->read('user_privileges', $queryParam)->render();
	}

	// CREATE
	public function create(array $parseBody): array
	{
		// SANITIZE PARAMS
		$parseBody = SanitizeParams::sanitize('user_privileges', $parseBody);
		// SALVA PRIVILEGES
		return $this->databaseActions->create('user_privileges', $parseBody + ['userCreator'=>UserLogged::getId()]);
	}

	// UPDATE
	public function update(array $parseBody):array
	{
		// SANITIZE PARAMS
		$parseBody = SanitizeParams::sanitize('user_privileges', $parseBody);
		$iduser_privileges = $parseBody['iduser_privileges'] ?? null;
		if (!$iduser_privileges) {
			return ['status'=>false, 'message'=>'iduser_privileges is required'];
		}
		// UPDATE PRIVILEGES
		return $this->databaseActions->update('user_privileges', $parseBody, ['iduser_privileges' => $iduser_privileges]);
	}

	// DELETE
	public function delete(array $queryParams): array
	{
		// SANITIZE PARAMS
		$queryParams = SanitizeParams::sanitize('user_privileges', $queryParams);
		$iduser = $queryParams['iduser_privileges'] ?? null;
		if (!$iduser) {
			return ['status'=>false, 'message'=>'iduser_privileges is required'];
		}
		// DELETAR PRIVILEGES
		return $this->databaseActions->delete('user_privileges', ['iduser_privileges' => $iduser]);
	}

}
