<?php
declare(strict_types=1);
namespace Plinct\Api;

use Plinct\Api\Request\Request;
use Plinct\Api\Request\User\User;
use Plinct\Api\Response\Response;
use Plinct\Api\Server\Server;
use Slim\App;

class ApiFactory
{
	/**
	 * @param App $slimApp
	 * @return PlinctApp
	 */
	public static function create(App $slimApp): PlinctApp {
		return new PlinctApp($slimApp);
	}

	/**
	 * @return User
	 */
	public static function user(): User
	{
		return new User();
	}

	/**
	 * @return Request
	 */
	public static function request(): Request
	{
		return new Request();
	}

	public static function response(): Response
	{
		return new Response();
	}
	/**
	 * @return Server
	 */
	public static function server(): Server
	{
		return new Server();
	}
}
