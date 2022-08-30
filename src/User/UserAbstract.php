<?php

declare(strict_types=1);

namespace Plinct\Api\User;

abstract class UserAbstract
{
	/**
	 * @var string|null
	 */
	protected static ?string $iduser = null;
	/**
	 * @var string|null
	 */
	private static ?string $name = null;
	/**
	 * @var string|null
	 */
	private static ?string $email = null;
	/**
	 * @var string|null
	 */
	private static ?string $password = null;
	/**
	 * @var array|null
	 */
	private static ?array $privileges = null;

	/**
	 * @param ?string $iduser
	 */
	protected static function setIduser(string $iduser)
	{
		self::$iduser = $iduser;
	}

	/**
	 * @return ?string
	 */
	public function getIduser(): ?string {
		return self::$iduser;
	}

	/**
	 * @param string $name
	 */
	protected static function setName(string $name): void
	{
		self::$name = $name;
	}

	/**
	 * @return ?string
	 */
	public static function getName(): ?string
	{
		return self::$name;
	}

	/**
	 * @param string $email
	 */
	protected static function setEmail(string $email): void
	{
		self::$email = $email;
	}

	/**
	 * @return string
	 */
	public static function getEmail(): string
	{
		return self::$email;
	}

	/**
	 * @param string $password
	 */
	protected static function setPassword(string $password): void
	{
		self::$password = $password;
	}

	/**
	 * @return string
	 */
	protected static function getPassword(): string
	{
		return self::$password;
	}

	/**
	 * @param array $privileges
	 */
	protected static function setPrivileges(array $privileges): void
	{
		self::$privileges = $privileges;
	}

	/**
	 *
	 */
	public static function getPrivileges(): ?array
	{
		return self::$privileges;
	}

	/**
	 * @return bool
	 */
	public static function isSuperUser(): bool {
		$privilegess = self::$privileges;
		 if ($privilegess) {
			foreach ($privilegess as $value) {
				if ($value['function'] == 5
					&& strpos($value['actions'], 'c') !== false
					&& strpos($value['actions'], 'r') !== false
					&& strpos($value['actions'], 'u') !== false
					&& strpos($value['actions'], 'd') !== false
					&& (isset($value['namespace']) && strpos($value['namespace'], 'all') !== false)
				) return true;
			}
		}
		return false;
	}
}