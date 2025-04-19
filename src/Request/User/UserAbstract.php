<?php
namespace Plinct\Api\Request\User;

abstract class UserAbstract
{
	/**
	 * @var int|null
	 */
	protected static ?int $iduser = null;
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
	 * @param int $iduser
	 */
	protected static function setIduser(int $iduser): void
	{
		self::$iduser = $iduser;
	}

	/**
	 * @return ?int
	 */
	public function getIduser(): ?int {
		return self::$iduser;
	}

	public static function iduser(): ?int {
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
					&& str_contains($value['action'], 'c')
					&& str_contains($value['action'], 'r')
					&& str_contains($value['action'], 'u')
					&& str_contains($value['action'], 'd')
					&& (isset($value['namespace']) && str_contains($value['namespace'], 'all'))
				) return true;
			}
		}
		return false;
	}
}