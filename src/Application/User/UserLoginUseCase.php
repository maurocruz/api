<?php
namespace Plinct\Api\Application\User;

use Plinct\Api\Domain\Configuration\ConfigurationDomain;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

readonly class UserLoginUseCase
{
	public function __construct(private DatabaseActions $databaseActions)
	{

	}

	public function login(array $params): array
	{
		$email = $params['email'] ?? null;
		$password = $params['password'] ?? null;

		// VERIFICA SE EXISTE EMAIL E PASSWORD EM PARAMS
		if (!$email || !$password) {
			return ['status' => false, 'message' => 'Email and password is required'];
		}

		// SANITIZAR EMAIL
		$email = filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL);
		if (!$email) {
			return ['status' => false, 'message' => 'Invalid email'];
		}

		// VERIFICA SE EMAIL CORRESPONDE A ALGUM USUÁRIO
		$userDatabase = $this->databaseActions->read('user', ['email' => $email,'fields' => 'iduser,name,password'])->render();
		if ($userDatabase['status'] === false) {
			return ['status' => false, 'message' => 'User not found'];
		}
		$user = $userDatabase['data'][0];
		$passwordHash = $user['password'];

		// VALIDA SENHA
		if (!password_verify($password, $passwordHash)) {
			return ['status' => false, 'message' => 'Invalid password'];
		}

		// CRIAR TOKEN
		$cretedToken = ConfigurationDomain::createAuthToken($user['iduser'], $user['name']);
		return ['status' => true, 'message' => 'Access authorized', 'data' => ['token' => $cretedToken]];
	}
}
