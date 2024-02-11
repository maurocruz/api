<?php

declare(strict_types=1);

namespace Plinct\Api\Server;

use Plinct\Api\Request\Server\ConnectBd;
use Plinct\Api\Server\GetData\GetData;
use Plinct\Api\Server\Relationship\Relationship;
use Plinct\Api\Type\Type;
use Plinct\Api\User\User;

class Server
{
	/**
	 * @return Maintenance
	 */
	public function configuration(): Maintenance
	{
		return new Maintenance();
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
	 * @param string $table
	 * @return GetData
	 */
	public function getDataInBd(string $table): GetData
	{
		return new GetData($table);
	}

	public function relationship(string $tableHasPart, string $idHasPart, string $tableIsPartOf, string $idIsPartOf): Relationship
	{
		return new Relationship($tableHasPart, $idHasPart, $tableIsPartOf, $idIsPartOf);
	}

	/**
	 * @param string $type
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}

	/**
	 * @return User
	 */
	public function user(): User
	{
		return new User();
	}
}
