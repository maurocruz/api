<?php
namespace Plinct\Api\Request\Server;

use Plinct\Api\Request\Server\ConnectBd\ConnectBd;
use Plinct\Api\Request\Server\GetData\GetData;

class Server
{
	/**
	 * @param string $table
	 * @return GetData
	 */
	public function getDataInBd(string $table): GetData
	{
		return new GetData($table);
	}

	/**
	 * @param ?string $table
	 * @return ConnectBd
	 */
	public function connectBd(?string $table = null): ConnectBd
	{
		return new ConnectBd($table);
	}
}
