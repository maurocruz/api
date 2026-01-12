<?php
declare(strict_types=1);
namespace Plinct\Api;

use Plinct\Api\Helper\Helper;
use Plinct\Api\Request\Request;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\User\User;
use Plinct\Api\Response\Response;
use Slim\App;

class ApiFactory
{
	/**
	 * @param App $slimApp
	 * @return ApiApp
	 */
	public static function create(App $slimApp): ApiApp
	{
		// for enabling routes PUT and DELETE
		$slimApp->addBodyParsingMiddleware();
		// error handling
		$slimApp->addErrorMiddleware(true,true,true);

		return new ApiApp($slimApp);
	}

	/**
	 * @return Helper
	 */
	public static function helper(): Helper
	{
		return new Helper();
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
	 * @param string $driver
	 * @param string $host
	 * @param string $dbname
	 * @param string $username
	 * @param string $password
	 * @param array $options
	 * @return void
	 */
	public static function connectBd(string $driver, string $host, string $dbname, string $username, string $password, array $options = []): void
	{
		PDOConnect::connect($driver, $host, $dbname, $username, $password, $options);
	}
}
