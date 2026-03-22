<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork;

use Plinct\Api\Application\Entities\Thing\ThingReadUseCase;
use ReflectionException;

class CreativeWorkReadUseCase extends ThingReadUseCase
{
	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		return $this->readEntity('CreativeWork', $queryParams);
	}
}
