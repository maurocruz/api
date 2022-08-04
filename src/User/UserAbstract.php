<?php

declare(strict_types=1);

namespace Plinct\Api\User;

abstract class UserAbstract
{
	private static ?string $iduser = null;
	private static ?int $exp = null;
	private static string $name;
	private static string $email;
	private static string $password;
	private static array $permission;

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
	public static function getIduser(): ?string
	{
		return self::$iduser;
	}

	/**
	 * @param int|null $exp
	 * @return void
	 */
	protected static function setExp(?int $exp): void
	{
		self::$exp = $exp;
	}

	/**
	 * @return int|null
	 */
	public function getExp(): ?int
	{
		return self::$exp;
	}
	/**
	 * @param string $name
	 */
	protected static function setName(string $name): void
	{
		self::$name = $name;
	}

	/**
	 * @return string
	 */
	public static function getName(): string
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
	 * @param array $permission
	 */
	protected static function setPermission(array $permission): void
	{
		self::$permission = $permission;
	}

	/**
	 *
	 */
	public static function getPermission(): array
	{
		return self::$permission;
	}

	/**
	 * @param bool $expiredToken
	 */
	public static function setExpiredToken(bool $expiredToken): void
	{
		self::$expiredToken = $expiredToken;
	}

	/**
	 * @return bool
	 */
	public static function isSuperUser(): bool {
		foreach (self::$permission as $value) {
			if ($value['function'] == 5
				&& strpos($value['actions'],'c') !== false
				&& strpos($value['actions'],'r') !== false
				&& strpos($value['actions'],'u') !== false
				&& strpos($value['actions'],'d') !== false
			) return true;
		}
		return false;
	}
}