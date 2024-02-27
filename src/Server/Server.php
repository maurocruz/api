<?php

declare(strict_types=1);

namespace Plinct\Api\Server;

use Plinct\Api\Request\Server\ConnectBd\ConnectBd;
use Plinct\Api\Request\Server\GetData\GetData;
use Plinct\Api\Request\Server\Relationship\Relationship;
use Plinct\Api\Request\Type\Type;
use Plinct\Api\Request\User\User;

class Server
{
	/**
	 * @param string $table
	 * @return ConnectBd
	 */
	public function connectBd(string $table): ConnectBd
	{
		return new ConnectBd($table);
	}

	/**
	 * @param string $table
	 * @return GetData
	 */
	public function getDataInBd(string $table): GetData
	{
		return new GetData($table);
	}

	public function relationship(string $tableHasPart, int $idHasPart, string $tableIsPartOf, int $idIsPartOf): Relationship
	{
		return new Relationship($tableHasPart, $idHasPart, $tableIsPartOf, $idIsPartOf);
	}

	/**
	 * @return User
	 */
	public function user(): User
	{
		return new User();
	}
}
