<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Configuration;

use Plinct\Api\Request\Configuration\Module\Module;
use Plinct\Api\Request\Configuration\Update\Update;

class Configuration
{
	/**
	 * @return Module
	 */
	public function module(): Module
	{
		return new Module();
	}

	/**
	 * @return Update
	 */
	public function update(): Update
	{
		return new Update();
	}
}
