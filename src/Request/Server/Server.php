<?php
declare(strict_types=1);
namespace Plinct\Api\Request\Server;

use Plinct\Api\Request\Server\ConnectBd\ConnectBd;
use Plinct\Api\Server\GetData\GetData;

class Server
{
	/**
	 * @var bool
	 */
	public static bool $isPermitted = false;

	/**
	 * @param string $table
	 * @return GetData
	 */
	public function getDataInBd(string $table): GetData
	{
		return new GetData($table);
	}

	/**
	 * @param string $table
	 * @return ConnectBd
	 */
	public function connectBd(string $table): ConnectBd
	{
		return new ConnectBd($table);
	}

	/**
	 * @param int $userFunction
	 * @param string $userAction
	 * @return $this
	 */
	public function needsPermission(int $userFunction, string $userAction): Server
	{
		if($userFunction === 5) {
			self::$isPermitted = true;
		}

		return $this;
	}
}
