<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration;

use Plinct\Api\Request\Configuration\Module\Module;

class Configuration
{
	/**
	 * @return Module
	 */
	public function module(): Module
	{
		return new Module();
	}
}
