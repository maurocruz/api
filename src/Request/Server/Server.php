<?php
namespace Plinct\Api\Request\Server;

use Plinct\Api\Request\Server\ConnectBd\ConnectBd;

class Server
{
	/**
	 * @param ?string $table
	 * @return ConnectBd
	 */
	public function connectBd(?string $table = null): ConnectBd
	{
		return new ConnectBd($table);
	}
}
