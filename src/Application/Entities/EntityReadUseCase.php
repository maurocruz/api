<?php
namespace Plinct\Api\Application\Entities;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use ReflectionException;

class EntityReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(string $type, array $queryParams = []): array
	{
		return $this->readEntity($type, $queryParams);
	}
}
