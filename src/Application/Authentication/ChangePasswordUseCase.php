<?php
namespace Plinct\Api\Application\Authentication;

use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Application\Support\SupportApplication;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

readonly class ChangePasswordUseCase
{
	public function __construct(
		private DatabaseActions $databaseActions
	) {	}

	public function changePassword(array $parseBody): array
	{
		// SANITIZE PARAMS
		$parseBody = SanitizeParams::sanitize('change_password', $parseBody);
		$selector = $parseBody['selector'] ?? null;
		$validator = $parseBody['validator'] ?? null;
		$password = $parseBody['password'] ?? null;
		$passwordConfirm = $parseBody['passwordConfirm'] ?? null;

		// VARIFICA SE EXISTEM TODOS OS DADOS
		if (!$selector || !$validator || !$password || !$passwordConfirm) {
			return SupportApplication::returnDTO(false, 'Missing required fields', $parseBody);
		}

		// VERIFICA SE PASSWORD CONFIRMA
		if ($password !== $passwordConfirm) {
			return SupportApplication::returnDTO(false, 'Passwords do not match', $parseBody);
		}

		// VERIFICA SE PASSWORD É VALIDO
		$validPass = SanitizeParams::validatePassword($password);
		if (is_string($validPass)) {
			return SupportApplication::returnDTO(false, $validPass, $parseBody);
		}

		// VERIFICA SE SELECTOR E VALIDATOR SÃO VÁLIDOS
		if (strlen($selector) !== 16 || strlen($validator) !== 64) {
			return SupportApplication::returnDTO(false, 'Invalid selector or validator', $parseBody);
		}

		// VERIFICA SE EXISTEM SELECTOR E VALIDATOR NO BD
		$dataReset = $this->databaseActions->read('user_passwordReset', ['fields' => 'iduser,token', 'selector' => $selector, 'where' => 'expires>=NOW()' ])->render();
		if ($dataReset['status'] === false) {
			return $dataReset;
		}
		$data = $dataReset['data'][0];

		// VERIFICA SE TOKEN É VALIDO
		$validatorBytes = hex2bin($validator);
		if ($validatorBytes === false) {
			return SupportApplication::returnDTO(false, 'Invalid or expired token', $parseBody);
		}
		$calculatedToken = hash('sha256', $validatorBytes);
		if (!hash_equals($data['token'], $calculatedToken)) {
			return SupportApplication::returnDTO(false, 'Invalid or expired token', $parseBody);
		}

		// CRIA HASH DE SENHA
		$iduser = $data['iduser'];
		$newPassword = password_hash($password, PASSWORD_DEFAULT);

		// SALVA NOVA SENHA
		$dataCreate = $this->databaseActions->update('user', ['password' => $newPassword], ['iduser' => $iduser]);
		if ($dataCreate['status'] === false) {
			return $dataCreate;
		}

		// APAGA TOKEN NO BD
		$tokenDelete = $this->databaseActions->delete('user_passwordReset', ['selector' => $selector, 'iduser' => $iduser]);
		if ($tokenDelete['status'] === false) {
			return $tokenDelete;
		}

		// RETORNA FINAL
		return SupportApplication::returnDTO(true, 'Password changed successfully');
	}
}
