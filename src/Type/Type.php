<?php

declare(strict_types=1);

namespace Plinct\Api\Type;

use Plinct\Api\Request\HttpRequest;

class Type
{
	private ?object $classActions = null;

	/**
	 * @param string $type
	 */
	public function __construct(string $type)
	{
		$classname = __NAMESPACE__ . '\\' . ucfirst($type);
		if (class_exists($classname)) {
			$this->classActions = new $classname();
		}
	}

	/**
	 * @return bool
	 */
	public function exists(): bool
	{
		return !!$this->classActions;
	}

	/**
	 * @return HttpRequest
	 */
	public function httpRequest(): HttpRequest
	{
		return new HttpRequest($this->classActions);
	}

	/**
	 * @param $type
	 * @return array
	 */
	public function create($type = null): array
	{
		return $this->classActions->createSqlTable();
	}
}