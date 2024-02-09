<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Routes;

class Routes
{
	/**
	 * @param $route
	 * @return mixed
	 */
	public function home($route) {
		$homeRouters = require __DIR__ . '/router.php';
		return $homeRouters($route);
	}

	public function auth($route) {
		$authRoutes = require __DIR__.'/authRoutes.php';
		return $authRoutes($route);
	}
	/**
	 * @param $route
	 * @return mixed
	 */
	public function user($route)	{
		$userRoutes = require __DIR__.'/userRoutes.php';
		return $userRoutes($route);
	}

	/**
	 * @param $route
	 * @return mixed
	 */
	public function userPrivileges($route) {
		$routePermissions = require __DIR__.'/userPrivilegesRoutes.php';
		return $routePermissions($route);
	}
}
