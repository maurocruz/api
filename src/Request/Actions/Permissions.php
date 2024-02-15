<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Actions;

class Permissions
{
	private static bool $requiresSubscription = false;

	private ?string $method = null;

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
