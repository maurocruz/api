<?php
declare(strict_types=1);
namespace Plinct\Api\Request;

use Plinct\Api\Request\Configuration\Configuration;
use Plinct\Api\Request\Routes\Routes;
use Plinct\Api\Request\Server\Server;
use Plinct\Api\Request\Type\Type;
use Plinct\Api\Request\User\User;

class Request
{
	/**
	 * @return Configuration
	 */
	public function configuration(): Configuration
	{
		return new Configuration();
	}
	/**
	 * @return Routes
	 */
	public function routes(): Routes {
		return new Routes();
	}
	/**
	 * @return Server
	 */
	public function server(): Server  {
		return new Server();
	}
	/**
	 * @param string $type
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}

	/**
	 * @return User
	 */
	public function user(): User
	{
		return new User();
	}
}
