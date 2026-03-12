<?php
namespace Plinct\Api\Domain\User;

class UserLogged
{
	private static int $id;
	private static string $name;
	private static array $privileges;

	/**
	 * @param int $id
	 */
	public static function setId(int $id): void
	{
		self::$id = $id;
	}

	/**
	 * @param string $name
	 */
	public static function setName(string $name): void
	{
		self::$name = $name;
	}

	/**
	 * @param array $privileges
	 */
	public static function setPrivileges(array $privileges): void
	{
		self::$privileges = $privileges;
	}

	/**
	 * @return int
	 */
	public static function getId(): int
	{
		return self::$id;
	}

	/**
	 * @return string
	 */
	public static function getName(): string
	{
		return self::$name;
	}

	/**
	 * @return array
	 */
	public static function getPrivileges(): array
	{
		return self::$privileges;
	}

	/**
	 * @param string $namespace
	 * @param string $action
	 * @param int $function
	 * @return bool
	 */
	public static function hasPermission(string $namespace, string $action, int $function): bool
	{
		$action = match ($action) {
			'create' => 'c',
			'read' => 'r',
			'update' => 'u',
			'delete' => 'd',
			default => ''
		};

		foreach (self::$privileges as $privilege) {
			if (($privilege['namespace'] === '*' || $privilege['namespace'] === 'all') && str_contains($privilege['action'], $action)) return true;
			if (str_contains($privilege['namespace'],$namespace) && str_contains($privilege['action'], $action) && $privilege['function'] > $function) return true;
		}

		return false;
	}
}
