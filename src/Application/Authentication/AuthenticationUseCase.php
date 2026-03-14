<?php
namespace Plinct\Api\Application\Authentication;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Plinct\Api\Application\Support\SanitizeParams;
use Plinct\Api\Infrastructure\Persistence\Database\DatabaseActions;

readonly class AuthenticationUseCase
{
	public function __construct(private DatabaseActions $databaseActions)
	{
	}

	public function register(array $params): array
	{
		// SANITIZE PARAMS
		$params = SanitizeParams::sanitize('auth', $params);
		$name = $params['name'] ?? null;
		$email = $params['email'] ?? null;
		$password = $params['password'] ?? null;
		$passwordConfirm = $params['passwordConfirm'] ?? null;

		// VERIFICA SE SENHAS SÃO IGUAIS
		if ($password !== $passwordConfirm) {
			return ['status'=>false, 'message'=>'Passwords do not match'];
		}
		// VERIFICA SE EXISTE NOME, EMAIL E SENHA
		if (!$name || !$email || !$password) {
			return ['status'=>false, 'message'=>'Name, email and password are required'];
		}
		// VERIFICA SE NÃO EXISTE USUÁRIO COM O MESMO EMAIL
		$userExists = $this->databaseActions->read('user', ['email' => $email])->render();
		if ($userExists['status'] === false) {
			return $userExists;
		}
		if (!empty($userExists['data'])) {
			return ['status'=>false, 'message'=>'User already exists'];
		}
		// VERIFICA DE EMAIL É VALIDO
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['status'=>false, 'message'=>'Invalid email'];
		}
		// VERIFICA SE NOME TEM MAIS DE 5 CARACTERES
		if (strlen($name) < 5) {
			return ['status'=>false, 'message'=>'Name must be at least 5 characters long'];
		}
		// VERIFICA SE SENHA TEM MAIS OU IGUAL A 8 CARACTERES, COM PELO MENOS 1 NUMERAL, 1 ALFABETO MAIÚSCULO E 1 ALFABETO MINÚSCULO E 1 CARACTER ESPECIAL
		if (!preg_match("#.*^(?=.{8,20})(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*\W).*$#", $password)) {
			return ['status'=>false, 'message'=>'Password must be at least 8 characters long, contain at least one uppercase letter, one lowercase letter, one number and one special character'];
		}

		// SAVE USER
		$newParams['name'] = $name;
		$newParams['email'] = $email;
		$newParams['password'] = password_hash($password, PASSWORD_DEFAULT);
		$database = $this->databaseActions->create('user', $newParams);
		if ($database['status'] === false) {
			return $database;
		}

		// OBTÉM IDUSER
		$iduser = $database['data'][0]['iduser'];

		// RETURN TRUE
		return ['status'=>true, 'message'=>'User created', 'data'=>['iduser'=>$iduser, 'name'=>$name]];
	}

	/**
	 * @return array
	 */
	public function validateToken(): array
	{
		$headers = getallheaders();
		$token = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
		try {
			$decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
			return ['status' => true, 'message' => "Token valid", "data" => $decoded];
		} catch (Exception $e) {
			return ['status' => false, 'message' => "Token invalid", "data" => $e];
		}
	}
}
