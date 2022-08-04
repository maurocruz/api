<?php

declare(strict_types=1);

namespace Plinct\Api\Interfaces;

use Plinct\Api\Request\Routes\Routes;
use Plinct\Api\Request\Server\Server;

interface RequestApiInterface
{
	public static function routes(): Routes;

	public static function server(): Server;
}