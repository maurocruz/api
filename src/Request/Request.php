<?php
declare(strict_types=1);
namespace Plinct\Api\Request;

use Plinct\Api\Request\Routes\Routes;
use Plinct\Api\Request\Server\Server;
use Plinct\Api\Request\User\User;
use Plinct\Api\Type\Type;

class Request
{
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
