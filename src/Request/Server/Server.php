<?php

declare(strict_types=1);

namespace Plinct\Api\Request\Server;

use Plinct\Api\Server\GetData\GetData;
use Plinct\Api\User\User;

class Server
{
	public function getDataInBd(string $table): GetData
	{
		return new GetData($table);
	}

	public function user(): User
	{
		return new User();
	}

	public function connectBd(string $table): ConnectBd
	{
		return new ConnectBd($table);
	}
}