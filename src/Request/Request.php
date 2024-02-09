<?php

declare(strict_types=1);

namespace Plinct\Api\Request;

use Plinct\Api\Request\Routes\Routes;
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
	 * @param string $type
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}
}
