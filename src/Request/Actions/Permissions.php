<?php
namespace Plinct\Api\Request\Actions;

class Permissions
{
	/**
	 * @var bool
	 */
	private static bool $requiresSubscription = false;

	/**
	 * @param bool $requiresSubscription
	 */
	public static function setRequiresSubscription(bool $requiresSubscription): void
	{
		self::$requiresSubscription = $requiresSubscription;
	}

	/**
	 * @return bool
	 */
	public static function isRequiresSubscription(): bool
	{
		return self::$requiresSubscription;
	}
}
