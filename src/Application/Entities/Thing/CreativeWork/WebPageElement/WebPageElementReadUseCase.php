<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPageElement;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use ReflectionException;

class WebPageElementReadUseCase extends CreativeWorkReadUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		return $this->readEntity('WebPageElement', $queryParams);
	}
}
