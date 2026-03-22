<?php
namespace Plinct\Api\Application\Entities\Thing\CreativeWork\WebPage;

use Plinct\Api\Application\Entities\Thing\CreativeWork\CreativeWorkReadUseCase;
use ReflectionException;

class WebPageReadUseCase extends CreativeWorkReadUseCase
{

	/**
	 * @throws ReflectionException
	 */
	public function read(array $queryParams = []): array
	{
		return $this->readEntity('WebPage', $queryParams);
	}
}
