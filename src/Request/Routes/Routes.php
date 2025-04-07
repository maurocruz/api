<?php
namespace Plinct\Api\Request\Routes;

class Routes
{
	/**
	 * @param $route
	 * @return mixed
	 */
	public function home($route): mixed
	{
		$homeRouters = require __DIR__ . '/router.php';
		return $homeRouters($route);
	}
	/**
	 * @param $route
	 * @return mixed
	 */
	public function user($route): mixed
	{
		$userRoutes = require __DIR__.'/userRoutes.php';
		return $userRoutes($route);
	}
}
