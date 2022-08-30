<?php

declare(strict_types=1);

namespace Plinct\Api\Request;

use Plinct\Api\Request\Routes\Routes;

class Request
{
	public function routes(): Routes
	{
		return new Routes();
	}
}
