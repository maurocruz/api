<?php

declare(strict_types=1);

namespace Plinct\Api\Request;

use Plinct\Api\Request\Routes\Routes;
use Plinct\Api\Request\Server\Server;

class RequestApi
{
	public static function routes(): Routes
	{
		return new Routes();
	}

	public static function server(): Server
	{
		return new Server();
	}
}
