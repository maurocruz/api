<?php
declare(strict_types=1);
namespace Plinct\Api;

use Plinct\Api\Request\Request;
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
		// for enable routes PUT and DELETE
		$slimApp->addBodyParsingMiddleware();
		// error handling
		$slimApp->addErrorMiddleware(true,true,true);

		return new ApiApp($slimApp);
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
}
