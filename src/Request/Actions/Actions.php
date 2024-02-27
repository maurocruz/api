<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Actions;

use Plinct\Api\Request\Server\HttpRequest;
use Plinct\Api\Request\Server\HttpRequestInterface;
use Plinct\Api\Request\User\Privileges\Privileges;

class Actions
{
	/**
	 * @return Permissions
	 */
	public static function permissions(): Permissions
	{
		return new Permissions();
	}

	public static function privileges(): Privileges
	{
		return new Privileges();
	}

	/**
	 * @param HttpRequestInterface $classActions
	 * @return HttpRequestInterface
	 */
	public function httpRequest(HttpRequestInterface $classActions): HttpRequestInterface {
		return new HttpRequest($classActions);
	}
}
