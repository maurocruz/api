<?php

declare(strict_types=1);

namespace Plinct\Api\Server;

interface DatabaseAccesseInterface
{
	public function setTable(string $table ): DatabaseAccesseInterface;

	public function setMethodRequest(string $method): DatabaseAccesseInterface;

	public function setParams(?array $params): DatabaseAccesseInterface;

	public function ready(): array;
}