<?php
declare(strict_types=1);
namespace Plinct\Api;

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Exception;
use Plinct\Api\Helper\Helper;
use Plinct\Api\Request\Request;
use Plinct\Api\Request\Server\ConnectBd\PDOConnect;
use Plinct\Api\Request\User\User;
use Plinct\Api\Response\Response;

class ApiFactory
{
	/**
	 * @throws Exception
	 */
	public static function create(array $settings): ApiApp
	{
		$debug = $settings['debug'] ?? false;

		// CONTAINER
		$builder = new ContainerBuilder();
		$builder->addDefinitions(['settings' => $settings]);
		$builder->addDefinitions(__DIR__ . '/Container/container.php');
		$container = $builder->build();

		// SLIM APP (middlewares and routes)
		$slimApp = Bridge::create($container);

		// MIDDLEWARES
		(require __DIR__ . '/Http/Middleware/middlewares.php')($slimApp, $settings);

		// ROUTES
		(require __DIR__ . '/../routes/routes.php')($slimApp);

		// RETURN
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
