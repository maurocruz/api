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
	/**
	 * @param $route
	 * @return mixed
	 */
	public function user($route)	{
		$userRoutes = require __DIR__.'/userRoutes.php';
		return $userRoutes($route);
	}
}
